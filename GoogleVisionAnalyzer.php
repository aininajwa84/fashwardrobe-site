<?php
// app/Services/AI/GoogleVisionAnalyzer.php

namespace App\Services\AI;

use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Feature\Type;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GoogleVisionAnalyzer implements ImageAnalyzerInterface
{
    private $client;
    private $isAvailable = false;

    public function __construct()
    {
       try{
        // Method 1: Using JSON credentials file
            $credentialsPath = storage_path('app/google-credentials.json');
            
            if (file_exists($credentialsPath)) {
                putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $credentialsPath);
                $this->client = new ImageAnnotatorClient();
                $this->isAvailable = true;
                Log::info('Google Vision API initialized with service account');
            }
            // Method 2: Using API Key (simpler)
            elseif (env('GOOGLE_VISION_API_KEY')) {
                $this->client = new ImageAnnotatorClient([
                    'credentials' => env('GOOGLE_VISION_API_KEY')
                ]);
                $this->isAvailable = true;
                Log::info('Google Vision API initialized with API key');
            }
            // Method 3: Try auto-discovery
            else {
                $this->client = new ImageAnnotatorClient();
                $this->isAvailable = true;
                Log::info('Google Vision API initialized with auto-discovery');
            }
            
        } catch (\Exception $e) {
            Log::error('Google Vision API initialization failed: ' . $e->getMessage());
            $this->isAvailable = false;
        }
    }

    public function analyze(string $imagePath): array
    {
        if (!$this->isAvailable || !$this->client) {
            throw new \Exception('Google Vision API is not available');
        }

        try {
            $imageContent = file_get_contents($imagePath);
            
            $response = $this->client->annotateImage(
                $imageContent,
                [
                    Type::LABEL_DETECTION,
                    Type::IMAGE_PROPERTIES,
                    Type::OBJECT_LOCALIZATION,
                    Type::LOGO_DETECTION,
                    Type::WEB_DETECTION
                ]
            );

            return [
                'provider' => 'google_vision',
                'labels' => $this->extractLabels($response),
                'colors' => $this->extractColors($response),
                'objects' => $this->extractObjects($response),
                'category' => $this->determineCategory($response),
                'color' => $this->determineDominantColor($response),
                'theme' => $this->determineTheme($response),
                'brand' => $this->detectBrand($response),
                'confidence' => $this->calculateConfidence($response),
                'raw_response' => $this->getSafeResponse($response)
            ];

        } catch (\Exception $e) {
            Log::error('Google Vision analysis failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function extractLabels($response): array
    {
        $labels = [];
        foreach ($response->getLabelAnnotations() as $label) {
            if ($label->getScore() > 0.7) {
                $labels[] = [
                    'description' => $label->getDescription(),
                    'score' => $label->getScore(),
                    'topicality' => $label->getTopicality()
                ];
            }
        }
        return $labels;
    }

    private function extractColors($response): array
    {
        $colors = [];
        $imageProperties = $response->getImagePropertiesAnnotation();
        
        if ($imageProperties) {
            $dominantColors = $imageProperties->getDominantColors()->getColors();
            foreach ($dominantColors as $colorInfo) {
                $color = $colorInfo->getColor();
                $colors[] = [
                    'red' => $color->getRed(),
                    'green' => $color->getGreen(),
                    'blue' => $color->getBlue(),
                    'score' => $colorInfo->getScore(),
                    'pixel_fraction' => $colorInfo->getPixelFraction(),
                    'hex' => sprintf("#%02x%02x%02x", $color->getRed(), $color->getGreen(), $color->getBlue())
                ];
            }
        }
        return $colors;
    }

    private function extractObjects($response): array
    {
        $objects = [];
        $objectAnnotations = $response->getLocalizedObjectAnnotations();
        
        if ($objectAnnotations) {
            foreach ($objectAnnotations as $object) {
                $objects[] = [
                    'name' => $object->getName(),
                    'score' => $object->getScore(),
                    'bounding_poly' => $this->formatBoundingPoly($object->getBoundingPoly())
                ];
            }
        }
        return $objects;
    }

    private function determineCategory($response): string
    {
        $clothingKeywords = [
            'top' => ['shirt', 't-shirt', 'blouse', 'top', 'sweater', 'hoodie', 'jacket', 'blazer', 'coat', 'sweatshirt'],
            'bottom' => ['pants', 'jeans', 'trousers', 'shorts', 'skirt', 'leggings', 'sweatpants'],
            'dress' => ['dress', 'gown', 'frock', 'evening dress', 'cocktail dress'],
            'shoes' => ['shoes', 'sneakers', 'heels', 'boots', 'sandals', 'footwear'],
            'accessory' => ['bag', 'hat', 'scarf', 'belt', 'jewelry', 'watch', 'glasses', 'sunglasses'],
            'outerwear' => ['jacket', 'coat', 'blazer', 'cardigan', 'parka', 'vest']
        ];

        $allLabels = array_merge(
            $this->extractLabels($response),
            $this->extractObjects($response)
        );

        foreach ($allLabels as $item) {
            $description = strtolower($item['name'] ?? $item['description'] ?? '');
            
            foreach ($clothingKeywords as $category => $keywords) {
                foreach ($keywords as $keyword) {
                    if (str_contains($description, $keyword) && ($item['score'] ?? 0.7) > 0.6) {
                        return $category;
                    }
                }
            }
        }

        return 'other';
    }

    private function determineDominantColor($response): string
    {
        $colorMap = [
            [[255, 0, 0], 'red'],
            [[0, 255, 0], 'green'],
            [[0, 0, 255], 'blue'],
            [[255, 255, 0], 'yellow'],
            [[255, 165, 0], 'orange'],
            [[128, 0, 128], 'purple'],
            [[255, 192, 203], 'pink'],
            [[0, 0, 0], 'black'],
            [[255, 255, 255], 'white'],
            [[128, 128, 128], 'gray'],
            [[165, 42, 42], 'brown'],
            [[0, 128, 128], 'teal'],
        ];

        $colors = $this->extractColors($response);
        
        if (!empty($colors)) {
            $dominant = $colors[0];
            $minDistance = PHP_INT_MAX;
            $closestColor = 'multicolor';

            foreach ($colorMap as [$rgb, $colorName]) {
                $distance = sqrt(
                    pow($dominant['red'] - $rgb[0], 2) +
                    pow($dominant['green'] - $rgb[1], 2) +
                    pow($dominant['blue'] - $rgb[2], 2)
                );

                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $closestColor = $colorName;
                }
            }

            return $closestColor;
        }

        return 'multicolor';
    }

    private function determineTheme($response): string
    {
        $themeKeywords = [
            'casual' => ['casual', 'everyday', 'cotton', 'denim', 'comfort', 'simple'],
            'formal' => ['formal', 'business', 'office', 'suit', 'dress shirt', 'professional'],
            'party' => ['party', 'evening', 'cocktail', 'celebration', 'glam', 'night'],
            'sport' => ['sport', 'athletic', 'gym', 'active', 'running', 'workout', 'exercise'],
            'beach' => ['beach', 'summer', 'swim', 'vacation', 'tropical', 'swimsuit'],
            'vintage' => ['vintage', 'retro', 'classic', 'old-fashioned'],
            'modern' => ['modern', 'contemporary', 'trendy', 'fashionable'],
        ];

        foreach ($this->extractLabels($response) as $label) {
            $description = strtolower($label['description']);
            
            foreach ($themeKeywords as $theme => $keywords) {
                foreach ($keywords as $keyword) {
                    if (str_contains($description, $keyword) && $label['score'] > 0.6) {
                        return $theme;
                    }
                }
            }
        }

        return 'casual';
    }

    private function detectBrand($response): ?string
    {
        $logoAnnotations = $response->getLogoAnnotations();
        
        if ($logoAnnotations) {
            foreach ($logoAnnotations as $logo) {
                if ($logo->getScore() > 0.8) {
                    return $logo->getDescription();
                }
            }
        }

        // Check text for brand names
        $textAnnotations = $response->getTextAnnotations();
        if ($textAnnotations && count($textAnnotations) > 0) {
            $commonBrands = ['nike', 'adidas', 'zara', 'h&m', 'uniqlo', 'gucci', 'prada', 'levi\'s'];
            $text = strtolower($textAnnotations[0]->getDescription());
            
            foreach ($commonBrands as $brand) {
                if (str_contains($text, $brand)) {
                    return ucfirst($brand);
                }
            }
        }

        return null;
    }

    private function calculateConfidence($response): float
    {
        $totalScore = 0;
        $count = 0;
        
        // Average scores from different detections
        foreach ($this->extractLabels($response) as $label) {
            $totalScore += $label['score'];
            $count++;
        }
        
        foreach ($this->extractObjects($response) as $object) {
            $totalScore += $object['score'];
            $count++;
        }

        return $count > 0 ? ($totalScore / $count) : 0.5;
    }

    private function formatBoundingPoly($boundingPoly): array
    {
        $points = [];
        foreach ($boundingPoly->getVertices() as $vertex) {
            $points[] = [
                'x' => $vertex->getX(),
                'y' => $vertex->getY()
            ];
        }
        return $points;
    }

    private function getSafeResponse($response): array
    {
        // Return a safe, serializable version of the response
        return [
            'label_count' => count($this->extractLabels($response)),
            'object_count' => count($this->extractObjects($response)),
            'color_count' => count($this->extractColors($response)),
            'has_logo' => !empty($response->getLogoAnnotations()),
            'has_text' => !empty($response->getTextAnnotations())
        ];
    }

    public function getName(): string
    {
        return 'Google Cloud Vision';
    }

    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    public function __destruct()
    {
        if ($this->client) {
            $this->client->close();
        }
    }
}