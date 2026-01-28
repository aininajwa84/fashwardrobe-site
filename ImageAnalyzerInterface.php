<?php
// app/Services/AI/ImageAnalyzerInterface.php

namespace App\Services\AI;

interface ImageAnalyzerInterface
{
    /**
     * Analyze clothing image and return structured data
     */
    public function analyze(string $imagePath): array;
    
    /**
     * Get provider name
     */
    public function getName(): string;
    
    /**
     * Check if provider is available
     */
    public function isAvailable(): bool;
}