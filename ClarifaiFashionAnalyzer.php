<?php
// app/Services/AI/ClarifaiFashionAnalyzer.php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ClarifaiFashionAnalyzer implements ImageAnalyzerInterface
{
    private $apiKey;
    private $baseUrl = 'https://api.clarifai.com/v2/models/';
    private $isAvailable = false;

    // Clarifai Fashion-specific models
    private $models = [
        'apparel' => '72c523807f93e18b431676fb9a58e6ad', // Apparel Detection
        'general' => 'aaa03c23b3724a16a56b629203edc62c', // General Image Recognition
        'color' => 'eeed0b6733a644cea07cf4c60f87ebb7', // Color Detection
        'texture' => 'afeb7ceb6fc74e47a535e03f26d8e0c4', // Texture & Pattern
    ];

    public function __construct()
    {
        $this->apiKey = env('CLARIFAI_API_KEY');
        $this->isAvailable = !empty($this->apiKey);
        
        if ($this->isAvailable) {
            Log::info('Clarifai API initialized');
        } else {
            Log::warning('Clarifai API key not found');
        }
    }

    public function analyze(string $imagePath): array
    {
        if (!$this->isAvailable) {
            throw new \Exception('Clarifai API is not available');
        }

        try {
            $imageBase64 = base64_encode(file_get_contents($imagePath));
            
            // Analyze with multiple fashion models in parallel
            $results = [
                'apparel' => $this->analyzeWithModel($imageBase64, 'apparel'),
                'color' => $this->analyzeWithModel($imageBase64, 'color'),
                'general' => $this->analyzeWithModel($imageBase64, 'general'),
            ];

            return [
                'provider' => 'clarifai_fashion',
                'apparel' => $this->parseApparelResults($results['apparel']),
                'colors' => $this->parseColorResults($results['color']),
                'general_tags' => $this->parseGeneralResults($results['general']),
                'category' => $this->determineCategory($results),
                'color' => $this->determineDominantColor($results['color']),
                'theme' => $this->determineTheme($results),
                'style' => $this->determineStyle($results['apparel']),
                'occasion' => $this->determineOccasion($results),
                'confidence' => $this->calculateConfidence($results),
                'raw_response' => $results
            ];

        } catch (\Exception $e) {
            Log::error('Clarifai analysis failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function analyzeWithModel(string $imageBase64, string $modelType): array
    {
        $cacheKey = 'clarifai_' . $modelType . '_' . md5($imageBase64);
        
        // Cache results for 1 hour to save API calls
        return Cache::remember($cacheKey, 3600, function () use ($imageBase64, $modelType) {
            $modelId = $this->models[$modelType] ?? $this->models['general'];
            
            $response = Http::withHeaders([
                'Authorization' => 'Key ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . $modelId . '/outputs', [
                'inputs' => [
                    [
                        'data' => [
                            'image' => [
                                'base64' => $imageBase64
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Clarifai API request failed: ' . $response->body());
        });
    }

    private function parseApparelResults(array $result): array
    {
        $apparelData = [];
        
        if (isset($result['outputs'][0]['data']['concepts'])) {
            foreach ($result['outputs'][0]['data']['concepts'] as $concept) {
                if ($concept['value'] > 0.5) { // Confidence threshold
                    $apparelData[] = [
                        'name' => $concept['name'],
                        'confidence' => $concept['value'],
                        'id' => $concept['id'] ?? null
                    ];
                }
            }
        }

        return $apparelData;
    }

    private function parseColorResults(array $result): array
    {
        $colors = [];
        
        if (isset($result['outputs'][0]['data']['colors'])) {
            foreach ($result['outputs'][0]['data']['colors'] as $color) {
                $colors[] = [
                    'name' => $color['w3c']['name'] ?? 'Unknown',
                    'hex' => $color['w3c']['hex'] ?? '#000000',
                    'value' => $color['value'] ?? 0,
                    'density' => $color['raw_hex'] ?? null
                ];
            }
        }

        return $colors;
    }

    private function parseGeneralResults(array $result): array
    {
        $tags = [];
        
        if (isset($result['outputs'][0]['data']['concepts'])) {
            foreach ($result['outputs'][0]['data']['concepts'] as $concept) {
                if ($concept['value'] > 0.7) {
                    $tags[] = [
                        'tag' => $concept['name'],
                        'confidence' => $concept['value']
                    ];
                }
            }
        }

        return $tags;
    }

    private function determineCategory(array $results): string
    {
        $apparelCategories = [
            'top' => ['shirt', 't-shirt', 'blouse', 'top', 'sweater', 'hoodie', 'jacket', 'blazer'],
            'bottom' => ['pants', 'jeans', 'trousers', 'shorts', 'skirt', 'leggings'],
            'dress' => ['dress', 'gown', 'frock', 'evening dress', 'cocktail dress'],
            'shoes' => ['shoes', 'sneakers', 'heels', 'boots', 'sandals', 'footwear'],
            'accessory' => ['bag', 'hat', 'scarf', 'belt', 'jewelry', 'glasses'],
            'outerwear' => ['jacket', 'coat', 'blazer', 'cardigan', 'parka'],
            'swimwear' => ['swimsuit', 'bikini', 'trunks', 'swimwear'],
        ];

        // Check apparel results first
        foreach ($results['apparel'] as $item) {
            $name = strtolower($item['name']);
            
            foreach ($apparelCategories as $category => $keywords) {
                foreach ($keywords as $keyword) {
                    if (str_contains($name, $keyword) && $item['confidence'] > 0.6) {
                        return $category;
                    }
                }
            }
        }

        // Check general tags as fallback
        foreach ($results['general_tags'] as $tag) {
            $tagName = strtolower($tag['tag']);
            
            foreach ($apparelCategories as $category => $keywords) {
                foreach ($keywords as $keyword) {
                    if (str_contains($tagName, $keyword) && $tag['confidence'] > 0.7) {
                        return $category;
                    }
                }
            }
        }

        return 'other';
    }

    private function determineDominantColor(array $colorResults): string
    {
        if (empty($colorResults['colors'])) {
            return 'multicolor';
        }

        // Get the color with highest value (dominance)
        $dominantColor = $colorResults['colors'][0];
        
        // Map color names to our standard
        $colorMapping = [
            'red' => ['red', 'crimson', 'scarlet', 'maroon'],
            'blue' => ['blue', 'navy', 'sky blue', 'royal blue'],
            'green' => ['green', 'emerald', 'lime', 'olive'],
            'black' => ['black', 'ebony', 'jet black'],
            'white' => ['white', 'ivory', 'cream'],
            'pink' => ['pink', 'rose', 'magenta', 'fuchsia'],
            'purple' => ['purple', 'violet', 'lavender', 'lilac'],
            'yellow' => ['yellow', 'gold', 'lemon', 'mustard'],
            'orange' => ['orange', 'tangerine', 'coral', 'peach'],
            'gray' => ['gray', 'grey', 'charcoal', 'slate'],
            'brown' => ['brown', 'tan', 'beige', 'chocolate', 'coffee'],
        ];

        $detectedColor = strtolower($dominantColor['name']);
        
        foreach ($colorMapping as $standardColor => $variations) {
            foreach ($variations as $variation) {
                if (str_contains($detectedColor, $variation)) {
                    return $standardColor;
                }
            }
        }

        return 'multicolor';
    }

    private function determineTheme(array $results): string
    {
        $themeKeywords = [
            'casual' => ['casual', 'everyday', 'streetwear', 'comfort', 'relaxed'],
            'formal' => ['formal', 'business', 'office', 'professional', 'elegant', 'sophisticated'],
            'party' => ['party', 'evening', 'cocktail', 'night', 'celebration', 'glamorous'],
            'sport' => ['sport', 'athletic', 'active', 'gym', 'workout', 'running'],
            'beach' => ['beach', 'summer', 'swim', 'vacation', 'tropical'],
            'vintage' => ['vintage', 'retro', 'classic', 'old-fashioned', 'antique'],
            'modern' => ['modern', 'contemporary', 'trendy', 'fashionable', 'stylish'],
            'bohemian' => ['bohemian', 'boho', 'ethnic', 'hippie'],
            'minimalist' => ['minimalist', 'simple', 'clean', 'minimal'],
        ];

        // Check apparel tags
        foreach ($results['apparel'] as $item) {
            $name = strtolower($item['name']);
            
            foreach ($themeKeywords as $theme => $keywords) {
                foreach ($keywords as $keyword) {
                    if (str_contains($name, $keyword) && $item['confidence'] > 0.6) {
                        return $theme;
                    }
                }
            }
        }

        // Check general tags
        foreach ($results['general_tags'] as $tag) {
            $tagName = strtolower($tag['tag']);
            
            foreach ($themeKeywords as $theme => $keywords) {
                foreach ($keywords as $keyword) {
                    if (str_contains($tagName, $keyword) && $tag['confidence'] > 0.7) {
                        return $theme;
                    }
                }
            }
        }

        return 'casual';
    }

    private function determineStyle(array $apparelResults): array
    {
        $styles = [];
        
        foreach ($apparelResults as $item) {
            if ($item['confidence'] > 0.7) {
                $styles[] = $item['name'];
            }
        }

        return array_slice($styles, 0, 5); // Top 5 styles
    }

    private function determineOccasion(array $results): array
    {
        $occasions = [];
        $occasionKeywords = [
            'work' => ['office', 'business', 'professional', 'work'],
            'party' => ['party', 'celebration', 'night out', 'event'],
            'wedding' => ['wedding', 'ceremony', 'bridal', 'formal event'],
            'date' => ['date', 'romantic', 'dinner', 'evening'],
            'travel' => ['travel', 'vacation', 'journey', 'trip'],
            'sports' => ['sports', 'gym', 'workout', 'exercise'],
            'casual' => ['casual', 'everyday', 'relaxed', 'comfort'],
        ];

        foreach ($results['general_tags'] as $tag) {
            $tagName = strtolower($tag['tag']);
            
            foreach ($occasionKeywords as $occasion => $keywords) {
                foreach ($keywords as $keyword) {
                    if (str_contains($tagName, $keyword) && $tag['confidence'] > 0.7) {
                        if (!in_array($occasion, $occasions)) {
                            $occasions[] = $occasion;
                        }
                    }
                }
            }
        }

        return empty($occasions) ? ['casual'] : $occasions;
    }

    private function calculateConfidence(array $results): float
    {
        $totalConfidence = 0;
        $count = 0;
        
        foreach ($results['apparel'] as $item) {
            $totalConfidence += $item['confidence'];
            $count++;
        }
        
        foreach ($results['general_tags'] as $tag) {
            $totalConfidence += $tag['confidence'];
            $count++;
        }

        return $count > 0 ? ($totalConfidence / $count) : 0.6;
    }

    public function getName(): string
    {
        return 'Clarifai Fashion AI';
    }

    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }
}