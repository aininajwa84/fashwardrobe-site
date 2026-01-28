<?php
// app/Services/AI/AIManager.php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AIManager
{
    private $providers = [];
    private $activeProviders = [];

    public function __construct()
    {
        // Initialize providers
        $this->providers = [
            'google_vision' => new GoogleVisionAnalyzer(),
            'clarifai' => new ClarifaiFashionAnalyzer(),
        ];

        // Determine active providers based on config
        $configProvider = env('AI_PROVIDER', 'both');
        
        if ($configProvider === 'both') {
            foreach ($this->providers as $name => $provider) {
                if ($provider->isAvailable()) {
                    $this->activeProviders[$name] = $provider;
                }
            }
        } elseif (isset($this->providers[$configProvider]) && $this->providers[$configProvider]->isAvailable()) {
            $this->activeProviders[$configProvider] = $this->providers[$configProvider];
        }

        Log::info('AI Manager initialized with providers: ' . implode(', ', array_keys($this->activeProviders)));
    }

    /**
     * Analyze image with all available providers
     */
    public function analyze(string $imagePath, bool $useCache = true): array
    {
        $cacheKey = 'ai_analysis_' . md5_file($imagePath);
        
        if ($useCache && Cache::has($cacheKey)) {
            Log::info('Returning cached AI analysis');
            return Cache::get($cacheKey);
        }

        $results = [];
        $errors = [];

        foreach ($this->activeProviders as $name => $provider) {
            try {
                Log::info("Starting analysis with {$name}");
                $startTime = microtime(true);
                
                $result = $provider->analyze($imagePath);
                $result['processing_time'] = round(microtime(true) - $startTime, 2);
                
                $results[$name] = $result;
                
                Log::info("{$name} analysis completed in {$result['processing_time']}s");
                
            } catch (\Exception $e) {
                $errors[$name] = $e->getMessage();
                Log::error("{$name} analysis failed: " . $e->getMessage());
            }
        }

        if (empty($results)) {
            throw new \Exception('All AI providers failed: ' . implode(', ', $errors));
        }

        // Merge results from multiple providers
        $finalResult = $this->mergeResults($results, $errors);
        
        if ($useCache) {
            Cache::put($cacheKey, $finalResult, now()->addHours(24));
        }

        return $finalResult;
    }

    /**
     * Merge results from multiple providers
     */
    private function mergeResults(array $providerResults, array $errors): array
    {
        $merged = [
            'success' => true,
            'providers_used' => array_keys($providerResults),
            'providers_failed' => array_keys($errors),
            'results' => $providerResults,
            'consensus' => $this->getConsensus($providerResults),
            'recommendation' => $this->generateRecommendation($providerResults),
            'confidence' => $this->calculateOverallConfidence($providerResults),
        ];

        return $merged;
    }

    /**
     * Get consensus from multiple providers
     */
    private function getConsensus(array $providerResults): array
    {
        $consensus = [
            'category' => null,
            'color' => null,
            'theme' => null,
            'notes' => []
        ];

        // Count votes for each category
        $categoryVotes = [];
        $colorVotes = [];
        $themeVotes = [];

        foreach ($providerResults as $provider => $result) {
            if (isset($result['category'])) {
                $categoryVotes[$result['category']] = ($categoryVotes[$result['category']] ?? 0) + 1;
            }
            
            if (isset($result['color'])) {
                $colorVotes[$result['color']] = ($colorVotes[$result['color']] ?? 0) + 1;
            }
            
            if (isset($result['theme'])) {
                $themeVotes[$result['theme']] = ($themeVotes[$result['theme']] ?? 0) + 1;
            }
        }

        // Get most voted
        $consensus['category'] = empty($categoryVotes) ? 'other' : array_search(max($categoryVotes), $categoryVotes);
        $consensus['color'] = empty($colorVotes) ? 'multicolor' : array_search(max($colorVotes), $colorVotes);
        $consensus['theme'] = empty($themeVotes) ? 'casual' : array_search(max($themeVotes), $themeVotes);

        return $consensus;
    }

    /**
     * Generate recommendation based on AI analysis
     */
    private function generateRecommendation(array $providerResults): array
    {
        $consensus = $this->getConsensus($providerResults);
        
        $recommendation = [
            'category' => $consensus['category'],
            'color' => $consensus['color'],
            'theme' => $consensus['theme'],
            'name_suggestion' => $this->generateNameSuggestion($consensus),
            'style_tips' => $this->generateStyleTips($consensus, $providerResults),
            'pairing_suggestions' => $this->generatePairingSuggestions($consensus),
        ];

        return $recommendation;
    }

    private function generateNameSuggestion(array $consensus): string
    {
        $color = ucfirst($consensus['color']);
        $category = ucfirst($consensus['category']);
        $theme = ucfirst($consensus['theme']);
        
        return "{$color} {$theme} {$category}";
    }

    private function generateStyleTips(array $consensus, array $providerResults): array
    {
        $tips = [];
        
        // Category-specific tips
        if ($consensus['category'] === 'dress') {
            $tips[] = 'Perfect for parties or formal events';
            $tips[] = 'Pair with heels and a clutch';
        } elseif ($consensus['category'] === 'top') {
            $tips[] = 'Great for layering';
            $tips[] = 'Pair with jeans or formal pants';
        } elseif ($consensus['category'] === 'shoes') {
            $tips[] = 'Complete your outfit with matching accessories';
        }

        // Color-specific tips
        if ($consensus['color'] === 'black') {
            $tips[] = 'Black goes well with any color!';
        } elseif (in_array($consensus['color'], ['red', 'yellow', 'orange'])) {
            $tips[] = 'Bright color - great for making a statement';
        } elseif ($consensus['color'] === 'white') {
            $tips[] = 'White is perfect for summer and clean looks';
        }

        // Theme-specific tips
        if ($consensus['theme'] === 'formal') {
            $tips[] = 'Suitable for office or business meetings';
        } elseif ($consensus['theme'] === 'sport') {
            $tips[] = 'Perfect for workouts and active days';
        } elseif ($consensus['theme'] === 'beach') {
            $tips[] = 'Great for vacations and summer outings';
        }

        return array_slice($tips, 0, 3); // Max 3 tips
    }

    private function generatePairingSuggestions(array $consensus): array
    {
        $pairings = [];
        
        if ($consensus['category'] === 'top') {
            $pairings[] = 'Jeans or trousers';
            $pairings[] = 'Skirt or shorts';
            $pairings[] = 'Blazer for formal look';
        } elseif ($consensus['category'] === 'bottom') {
            $pairings[] = 'Simple t-shirt or blouse';
            $pairings[] = 'Sweater or cardigan';
        } elseif ($consensus['category'] === 'dress') {
            $pairings[] = 'Cardigan or jacket';
            $pairings[] = 'Statement jewelry';
            $pairings[] = 'Heels or sandals';
        }

        return $pairings;
    }

    private function calculateOverallConfidence(array $providerResults): float
    {
        $totalConfidence = 0;
        $count = 0;
        
        foreach ($providerResults as $result) {
            if (isset($result['confidence'])) {
                $totalConfidence += $result['confidence'];
                $count++;
            }
        }

        return $count > 0 ? ($totalConfidence / $count) : 0.7;
    }

    /**
     * Get list of available providers
     */
    public function getAvailableProviders(): array
    {
        $available = [];
        
        foreach ($this->providers as $name => $provider) {
            if ($provider->isAvailable()) {
                $available[$name] = $provider->getName();
            }
        }
        
        return $available;
    }

    /**
     * Get provider statistics
     */
    public function getStats(): array
    {
        return [
            'total_providers' => count($this->providers),
            'available_providers' => count($this->activeProviders),
            'provider_names' => $this->getAvailableProviders(),
        ];
    }
}