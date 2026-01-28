<?php
// GoogleVisionTest.php
// Save in: C:\laragon\www\smart_wardrobe\

echo "=== GOOGLE CLOUD VISION API TEST ===\n\n";

// Method 1: Test with direct API call (NO COMPOSER REQUIRED)
function testWithDirectAPI($apiKey) {
    echo "Testing with direct API call...\n";
    
    $url = "https://vision.googleapis.com/v1/images:annotate?key=" . $apiKey;
    
    $data = [
        'requests' => [
            [
                'image' => [
                    'source' => [
                        'imageUri' => 'https://storage.googleapis.com/cloud-samples-data/vision/using_curl/shanghai.jpeg'
                    ]
                ],
                'features' => [
                    ['type' => 'LABEL_DETECTION', 'maxResults' => 5]
                ]
            ]
        ]
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen(json_encode($data))
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP Status Code: $httpCode\n";
    
    if ($httpCode === 200) {
        $result = json_decode($response, true);
        echo "✅ SUCCESS! Google Vision API is working!\n\n";
        
        if (isset($result['responses'][0]['labelAnnotations'])) {
            echo "Detected labels:\n";
            foreach ($result['responses'][0]['labelAnnotations'] as $label) {
                echo "- {$label['description']} (confidence: " . round($label['score'] * 100) . "%)\n";
            }
        }
        return true;
    } else {
        echo "❌ FAILED!\n";
        echo "Response: " . substr($response, 0, 500) . "\n";
        return false;
    }
}

// Method 2: Test if Google Cloud Vision package is installed
function testPackageInstallation() {
    echo "\n\n=== CHECKING PACKAGE INSTALLATION ===\n";
    
    if (class_exists('Google\Cloud\Vision\V1\ImageAnnotatorClient')) {
        echo "✅ Google Cloud Vision package is installed!\n";
        return true;
    } else {
        echo "❌ Google Cloud Vision package is NOT installed.\n";
        echo "Run this command to install:\n";
        echo "composer require google/cloud-vision\n";
        return false;
    }
}

// Method 3: Simple environment check
function checkEnvironment() {
    echo "\n\n=== ENVIRONMENT CHECK ===\n";
    
    // Check .env file
    $envFile = __DIR__ . '/.env';
    if (file_exists($envFile)) {
        echo "✅ .env file exists\n";
        
        $envContent = file_get_contents($envFile);
        if (strpos($envContent, 'GOOGLE_VISION_API_KEY') !== false) {
            echo "✅ GOOGLE_VISION_API_KEY found in .env\n";
        } else {
            echo "❌ GOOGLE_VISION_API_KEY NOT found in .env\n";
        }
    } else {
        echo "❌ .env file not found\n";
    }
    
    // Check if running from Laragon
    echo "PHP Version: " . PHP_VERSION . "\n";
    echo "Current Directory: " . __DIR__ . "\n";
}

// Main test execution
function main() {
    echo "Starting Google Cloud Vision Test...\n";
    echo "====================================\n\n";
    
    // 1. Check environment
    checkEnvironment();
    
    // 2. Get API key from user or .env
    $apiKey = '';
    
    // Try to read from .env
    $envFile = __DIR__ . '/.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with($line, 'GOOGLE_VISION_API_KEY=')) {
                $apiKey = substr($line, 22); // Remove "GOOGLE_VISION_API_KEY="
                break;
            }
        }
    }
    
    if (empty($apiKey)) {
        echo "\n❌ No API key found in .env file.\n";
        echo "Please enter your Google Vision API Key: ";
        $apiKey = trim(fgets(STDIN));
        
        if (empty($apiKey)) {
            echo "API key is required. Exiting.\n";
            exit(1);
        }
    } else {
        echo "\n✅ Found API key in .env (first 10 chars): " . substr($apiKey, 0, 10) . "...\n";
    }
    
    // 3. Test the API
    echo "\n\n=== TESTING API CONNECTION ===\n";
    testWithDirectAPI($apiKey);
    
    // 4. Check package
    testPackageInstallation();
    
    echo "\n\n=== TEST COMPLETE ===\n";
    echo "If you see 'SUCCESS! Google Vision API is working!' above,\n";
    echo "then your API key is correct and ready to use!\n";
}

// Run the test
main();