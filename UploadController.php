<?php
// app/Http\Controllers/UploadController.php - UPDATED

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wardrobe;
use App\Services\GoogleVisionService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    protected $visionService;
    
    public function __construct(GoogleVisionService $visionService)
    {
        $this->visionService = $visionService;
    }
    
    /**
     * Show upload form
     */
    public function create()
    {
        return view('upload.create', [
            'aiAvailable' => true,
            'aiName' => 'Google Vision AI',
            'aiDescription' => 'Powered by Google Cloud Vision API'
        ]);
    }
    
    /**
     * Analyze image with AI
     */
    public function analyze(Request $request)
    {
        Log::info('=== ANALYZE METHOD CALLED ===');
        Log::info('File received: ' . ($request->hasFile('image') ? 'Yes' : 'No'));
        
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);
        
        try {
            // Store image temporarily
            Log::info('Storing temporary file...');
            $tempPath = $request->file('image')->store('temp/upload', 'public');
            $fullPath = storage_path('app/public/' . $tempPath);
            
            Log::info('Temp file stored at: ' . $fullPath);
            Log::info('File exists: ' . (file_exists($fullPath) ? 'Yes' : 'No'));
            Log::info('File size: ' . filesize($fullPath) . ' bytes');
            
            // Check file
            if (!file_exists($fullPath)) {
                throw new \Exception('Temporary file not created: ' . $fullPath);
            }
            
            // AI Analysis
            Log::info('Calling Google Vision service...');
            $analysis = $this->visionService->analyzeClothing($fullPath);
            
            Log::info('Analysis result:', [
                'success' => $analysis['success'] ?? false,
                'error' => $analysis['error'] ?? 'No error',
                'category' => $analysis['category'] ?? 'Unknown',
                'provider' => $analysis['provider'] ?? 'Unknown'
            ]);
            
            // Delete temp file
            if (Storage::disk('public')->exists($tempPath)) {
                Storage::disk('public')->delete($tempPath);
                Log::info('Temp file deleted: ' . $tempPath);
            }
            
            if (!$analysis['success']) {
                $error = $analysis['error'] ?? 'AI analysis failed';
                Log::error('Analysis unsuccessful: ' . $error);
                
                return response()->json([
                    'success' => false,
                    'message' => 'AI analysis failed: ' . $error,
                    'analysis' => $analysis
                ], 422);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'AI analysis completed successfully!',
                'analysis' => $analysis,
                'category' => $analysis['category'],
                'color' => $analysis['color'],
                'theme' => $analysis['theme'],
                'confidence' => $analysis['confidence'],
                'labels' => array_slice($analysis['labels'], 0, 5),
                'objects' => array_slice($analysis['objects'], 0, 3),
                'colors' => array_slice($analysis['colors'], 0, 3),
                'provider' => $analysis['provider']
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error: ' . json_encode($e->errors()));
            throw $e; // Let Laravel handle validation errors
        } catch (\Exception $e) {
            Log::error('Upload analyze error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Clean up any temp files
            if (isset($tempPath) && Storage::disk('public')->exists($tempPath)) {
                Storage::disk('public')->delete($tempPath);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'AI analysis failed. Please try again.',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Save item to wardrobe
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'category' => 'required|string|max:50',
            'color' => 'required|string|max:50',
            'theme' => 'nullable|string|max:50',
            'occasion' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'ai_analysis' => 'nullable|string' // Changed from array to string for JSON
        ]);
        
        try {
            Log::info('Storing wardrobe item...');
            
            // Store image
            $imagePath = $request->file('image')->store('wardrobe/' . date('Y/m'), 'public');
            
            // Generate name if not provided
            $name = $validated['name'] ?? $this->generateName(
                $validated['category'], 
                $validated['color']
            );
            
            // Parse AI analysis if provided
            $aiAnalysis = null;
            if (!empty($validated['ai_analysis'])) {
                try {
                    $aiAnalysis = is_string($validated['ai_analysis']) 
                        ? json_decode($validated['ai_analysis'], true)
                        : $validated['ai_analysis'];
                } catch (\Exception $e) {
                    Log::warning('Failed to parse AI analysis: ' . $e->getMessage());
                }
            }
            
            // Create wardrobe item
            $wardrobe = Wardrobe::create([
                'user_id' => Auth::id(),
                'name' => $name,
                'category' => $validated['category'],
                'color' => $validated['color'],
                'theme' => $validated['theme'] ?? 'casual',
                'occasion' => $validated['occasion'] ?? $validated['theme'] ?? 'general',
                'notes' => $validated['notes'] ?? 'Added with AI analysis',
                'image' => $imagePath,
                'ai_analysis' => json_encode($aiAnalysis),
                'is_favorite' => false,
            ]);
            
            Log::info('Wardrobe item created: ID ' . $wardrobe->id);
            
            return redirect()->route('wardrobe.index')
                ->with('success', 'Item added successfully with AI analysis!');
                
        } catch (\Exception $e) {
            Log::error('Upload store error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to save item: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Quick upload with auto-detection
     */
    public function quickUpload(Request $request)
    {
        Log::info('=== QUICK UPLOAD CALLED ===');
        
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);
        
        try {
            // Store image temporarily first
            $tempPath = $request->file('image')->store('temp/quick', 'public');
            $tempFullPath = storage_path('app/public/' . $tempPath);
            
            Log::info('Temp image stored: ' . $tempPath);
            
            // AI Analysis
            Log::info('Calling Google Vision for analysis...');
            $analysis = $this->visionService->analyzeClothing($tempFullPath);
            
            Log::info('Quick upload analysis result:', [
                'success' => $analysis['success'] ?? false,
                'category' => $analysis['category'] ?? 'Unknown'
            ]);
            
            if (!$analysis['success']) {
                // Delete temp file
                Storage::disk('public')->delete($tempPath);
                
                $error = $analysis['error'] ?? 'AI analysis failed';
                Log::error('Quick upload analysis failed: ' . $error);
                
                return response()->json([
                    'success' => false,
                    'message' => 'AI detection failed: ' . $error
                ], 422);
            }
            
            // Now store the image permanently
            $imagePath = $request->file('image')->store('wardrobe/' . date('Y/m'), 'public');
            
            // Auto-create item
            $wardrobe = Wardrobe::create([
                'user_id' => Auth::id(),
                'name' => $this->generateName($analysis['category'], $analysis['color']),
                'category' => $analysis['category'],
                'color' => $analysis['color'],
                'theme' => $analysis['theme'],
                'occasion' => $analysis['theme'],
                'notes' => 'Auto-detected by Google Vision AI. ' . 
                          'Confidence: ' . round($analysis['confidence'] * 100) . '%',
                'image' => $imagePath,
                'ai_analysis' => json_encode($analysis),
                'is_favorite' => false,
                'ai_detected' => true
            ]);
            
            // Delete temp file
            Storage::disk('public')->delete($tempPath);
            
            Log::info('Quick upload successful: ID ' . $wardrobe->id);
            
            return response()->json([
                'success' => true,
                'message' => 'Item auto-added with AI detection!',
                'item' => [
                    'id' => $wardrobe->id,
                    'name' => $wardrobe->name,
                    'category' => $wardrobe->category,
                    'color' => $wardrobe->color,
                    'theme' => $wardrobe->theme,
                    'image_url' => Storage::url($wardrobe->image)
                ],
                'analysis' => $analysis
            ]);
            
        } catch (\Exception $e) {
            Log::error('Quick upload error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Clean up temp files
            if (isset($tempPath) && Storage::disk('public')->exists($tempPath)) {
                Storage::disk('public')->delete($tempPath);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Auto-upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generate auto name
     */
    private function generateName($category, $color)
    {
        $categoryNames = [
            'top' => 'Top',
            'bottom' => 'Bottom',
            'dress' => 'Dress',
            'shoes' => 'Shoes',
            'outerwear' => 'Outerwear',
            'accessory' => 'Accessory',
            'other' => 'Item'
        ];
        
        $categoryName = $categoryNames[$category] ?? 'Clothing';
        return ucfirst($color) . ' ' . $categoryName;
    }
}