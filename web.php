<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WardrobeController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes - Smart Wardrobe System
|--------------------------------------------------------------------------
*/

// Landing Page
Route::get('/', function () {
    return view('auth.login');
});

// Dashboard
Route::middleware('auth')->group(function() {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /* =====================
        MY WARDROBE (CRUD)
    ===================== */
    Route::resource('wardrobe', WardrobeController::class);
    
    Route::get('/wardrobe/{id}', [WardrobeController::class, 'show'])->name('wardrobe.show');
    
    // Debug routes (temporary - remove in production)
    Route::get('/check-wardrobe', function() {
        $user = auth()->user();
        $wardrobes = $user->wardrobes()->get();
        
        return response()->json([
            'user' => $user->email,
            'total_items' => $wardrobes->count(),
            'items' => $wardrobes->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'theme' => $item->theme,
                    'category' => $item->category,
                    'color' => $item->color,
                    'occasion' => $item->occasion
                ];
            })
        ]);
    });

    Route::get('/add-test-wardrobe', function() {
        $user = auth()->user();
        
        // Clear first
        $user->wardrobes()->delete();
        
        // Add test items with various themes
        $testItems = [
            [
                'name' => 'Blue Formal Shirt',
                'category' => 'top',
                'color' => 'blue',
                'theme' => 'formal',
                'occasion' => 'work,office',
                'is_favorite' => true
            ],
            [
                'name' => 'Black Dress Pants',
                'category' => 'bottom',
                'color' => 'black',
                'theme' => 'formal',
                'occasion' => 'business,formal',
                'is_favorite' => false
            ],
            [
                'name' => 'Casual White T-Shirt',
                'category' => 'top',
                'color' => 'white',
                'theme' => 'casual',
                'occasion' => 'weekend,outdoor'
            ],
            [
                'name' => 'Party Dress',
                'category' => 'dress',
                'color' => 'red',
                'theme' => 'party',
                'occasion' => 'night out,event'
            ],
            [
                'name' => 'Beach Shorts',
                'category' => 'bottom',
                'color' => 'blue',
                'theme' => 'beach',
                'occasion' => 'outdoor,summer'
            ]
        ];
        
        foreach ($testItems as $item) {
            $user->wardrobes()->create($item);
        }
        
        return redirect('/check-wardrobe')
            ->with('success', 'Test wardrobe items added!');
    });

    /* ====================
        UPLOAD PHOTO 
    ==================== */
    Route::prefix('upload')->group(function() {
        Route::get('/', [UploadController::class, 'create'])->name('upload.create');
        Route::post('/analyze', [UploadController::class, 'analyze'])->name('upload.analyze');
        Route::post('/', [UploadController::class, 'store'])->name('upload.store');
        Route::post('/quick', [UploadController::class, 'quickUpload'])->name('upload.quick');
    });

    /* ====================
        RECOMMENDATION
    ==================== */
    Route::prefix('recommendation')->name('recommendation.')->group(function () {
        Route::get('/', [RecommendationController::class, 'index'])->name('index');
        Route::post('/generate', [RecommendationController::class, 'generate'])->name('generate');
        Route::get('/result', [RecommendationController::class, 'result'])->name('result');
        Route::get('/online', [RecommendationController::class, 'online'])->name('online');
        Route::get('/search-online', [RecommendationController::class, 'getOnlineRecommendations'])->name('search.online');
        Route::get('/telegram-search/{theme}', function($theme) {
            $botUsername = 'smartwardrobe_search_bot'; 
            
            // Generate Telegram link
            $telegramLink = "https://t.me/{$botUsername}?start=search_{$theme}";
            
            // QR code for mobile users
            $qrCode = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($telegramLink);
            
            return view('recommendation.telegram-redirect', [
                'telegramLink' => $telegramLink,
                'qrCode' => $qrCode,
                'theme' => $theme
            ]);
        })->name('telegram.search');
        
        Route::post('/generate-with-telegram', function(\Illuminate\Http\Request $request) {
            $request->validate(['theme' => 'required']);
            
            if ($request->items_count == 0) {
                // Redirect to Telegram search
                return redirect()->route('recommendation.telegram.search', [
                    'theme' => $request->theme
                ]);
            }
            
            return redirect()->route('recommendation.result');
        })->name('generate.with-telegram');
    });

    /* ====================
        DEBUG ROUTES
    ==================== */
    Route::get('/debug-upload', function() {
        $visionService = new \App\Services\GoogleVisionService();
        
        // Check if a test image exists
        $testImagePath = public_path('test-clothing.jpg');
        
        if (!file_exists($testImagePath)) {
            $testImagePath = storage_path('app/public/test-image.jpg');
            if (!file_exists($testImagePath)) {
                $testUrl = 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80';
                $imageContent = @file_get_contents($testUrl);
                if ($imageContent) {
                    file_put_contents($testImagePath, $imageContent);
                }
            }
        }
        
        if (file_exists($testImagePath)) {
            $result = $visionService->analyzeClothing($testImagePath);
            
            return response()->json([
                'test_image' => $testImagePath,
                'file_exists' => file_exists($testImagePath),
                'file_size' => filesize($testImagePath),
                'api_key_set' => !empty(env('GOOGLE_VISION_API_KEY')),
                'api_key' => substr(env('GOOGLE_VISION_API_KEY', ''), 0, 10) . '...',
                'api_key_length' => strlen(env('GOOGLE_VISION_API_KEY') ?? ''),
                'result' => $result
            ]);
        }
        
        return response()->json([
            'error' => 'Test image not found or created',
            'test_path' => $testImagePath
        ]);
    });

    // Test API key route
    Route::get('/check-api-key', function() {
        $apiKey = env('GOOGLE_VISION_API_KEY');
        
        return response()->json([
            'api_key_in_env' => !empty($apiKey),
            'api_key_length' => strlen($apiKey ?? ''),
            'api_key_starts_with' => substr($apiKey ?? '', 0, 6),
            'expected_start' => 'AIzaSy',
            'is_valid_format' => substr($apiKey ?? '', 0, 6) === 'AIzaSy',
            'env_key_preview' => $apiKey ? substr($apiKey, 0, 10) . '...' : 'Not set',
            'full_key' => env('APP_DEBUG') ? $apiKey : substr($apiKey ?? '', 0, 10) . '...'
        ]);
    });

    // Test vision directly
    Route::get('/test-google-vision', function() {
        $apiKey = env('GOOGLE_VISION_API_KEY');
        
        if (empty($apiKey)) {
            return response()->json(['error' => 'API key not set in .env']);
        }
        
        // Simple test image URL
        $testImageUrl = 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80';
        $imageContent = @file_get_contents($testImageUrl);
        
        if (!$imageContent) {
            return response()->json(['error' => 'Failed to fetch test image']);
        }
        
        $base64Image = base64_encode($imageContent);
        
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(30)->post(
                "https://vision.googleapis.com/v1/images:annotate?key={$apiKey}",
                [
                    'requests' => [
                        [
                            'image' => ['content' => $base64Image],
                            'features' => [
                                ['type' => 'LABEL_DETECTION', 'maxResults' => 5]
                            ]
                        ]
                    ]
                ]
            );
            
            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'status' => 'SUCCESS! API Key is working!',
                    'labels_found' => isset($data['responses'][0]['labelAnnotations']) ? count($data['responses'][0]['labelAnnotations']) : 0,
                    'sample_labels' => isset($data['responses'][0]['labelAnnotations']) ? 
                        array_slice($data['responses'][0]['labelAnnotations'], 0, 3) : []
                ]);
            } else {
                return response()->json([
                    'status' => 'ERROR',
                    'status_code' => $response->status(),
                    'error' => $response->body()
                ], 500);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'EXCEPTION',
                'error' => $e->getMessage()
            ], 500);
        }
    });

    /* ====================
        USER PROFILE
    ==================== */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update.password');
    Route::post('/profile/notifications', [ProfileController::class, 'updateNotifications'])->name('profile.update.notifications');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Add to web.php - test the API key
Route::get('/test-api-validation', function() {
    $apiKey = 'AIzaSyCrQSlJRmp7MUh2e29jGNa6o1kVQ6m5BRo';
    
    // Test 1: Basic validation
    $tests = [
        'api_key_length' => strlen($apiKey),
        'expected_length' => 39,
        'api_key_starts_with' => substr($apiKey, 0, 6),
        'expected_start' => 'AIzaSy',
        'is_valid_format' => substr($apiKey, 0, 6) === 'AIzaSy',
    ];
    
    // Test 2: Direct API call
    $url = "https://vision.googleapis.com/v1/images:annotate?key={$apiKey}";
    $testData = [
        'requests' => [
            [
                'image' => [
                    'source' => [
                        'imageUri' => 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b'
                    ]
                ],
                'features' => [
                    ['type' => 'LABEL_DETECTION', 'maxResults' => 1]
                ]
            ]
        ]
    ];
    
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $tests['api_test_http_code'] = $httpCode;
        $tests['api_test_success'] = $httpCode === 200;
        
        if ($httpCode !== 200) {
            $tests['api_error'] = json_decode($response, true) ?? $response;
        }
    } catch (\Exception $e) {
        $tests['api_test_error'] = $e->getMessage();
    }
    
    return response()->json($tests);
});
// Breeze Auth Routes (login, register, logout)
require __DIR__.'/auth.php';