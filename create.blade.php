{{-- resources/views/upload/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Upload with AI - Smart Wardrobe')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-purple-50 py-8">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full mb-6">
                <i class="fab fa-google text-white text-3xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Upload with Google AI</h1>
            <p class="text-gray-600 text-lg">
                Upload a photo and let Google Vision AI detect clothing details automatically!
            </p>
        </div>

        <!-- Upload Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left: Upload -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-cloud-upload-alt text-blue-500 mr-2"></i>
                    Upload Photo
                </h2>
                
                <!-- Drag & Drop Area -->
                <div id="uploadArea" 
                     class="border-4 border-dashed border-gray-300 rounded-2xl p-12 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition duration-300 mb-8">
                    <i class="fas fa-image text-6xl text-gray-400 mb-6"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-3">
                        Drop clothing photo here
                    </h3>
                    <p class="text-gray-500 mb-6">
                        JPG, PNG, GIF • Max 5MB
                    </p>
                    <input type="file" id="imageInput" accept="image/*" class="hidden">
                    <button onclick="document.getElementById('imageInput').click()" 
                            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold rounded-xl hover:from-blue-700 hover:to-purple-700 transition shadow-lg">
                        <i class="fas fa-camera mr-2"></i> Choose Photo
                    </button>
                </div>

                <!-- Preview -->
                <div id="imagePreview" class="hidden mb-8">
                    <h3 class="font-bold text-gray-700 mb-4">Preview</h3>
                    <div class="relative">
                        <img id="previewImage" 
                             class="w-full h-64 object-cover rounded-xl border border-gray-200 shadow">
                        <div id="loadingSpinner" 
                             class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center rounded-xl hidden">
                            <div class="text-center">
                                <div class="w-16 h-16 border-4 border-white border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                                <p class="text-white font-semibold text-lg">Google AI Analyzing...</p>
                                <p class="text-white text-sm">Detecting clothing details</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Upload Button -->
                <button id="quickUploadBtn" 
                        class="w-full py-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-bold rounded-xl hover:from-green-600 hover:to-emerald-700 transition shadow-lg hidden">
                    <i class="fas fa-bolt mr-2"></i> Auto-Add with AI Detection
                </button>
            </div>

            <!-- Right: AI Results -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center mr-4">
                        <i class="fas fa-robot text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Google Vision AI Results</h2>
                        <p class="text-gray-600">Detected details will appear here</p>
                    </div>
                </div>

                <!-- AI Analysis Results -->
                <div id="aiResults" class="hidden">
                    <!-- Confidence Indicator -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-medium text-gray-700">AI Confidence</span>
                            <span id="confidenceValue" class="font-bold text-green-600">85%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div id="confidenceBar" class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full transition-all duration-500" style="width: 85%"></div>
                        </div>
                    </div>

                    <!-- Detected Details -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-xl">
                            <p class="text-sm text-gray-500 mb-1">Category</p>
                            <p id="detectedCategory" class="font-bold text-blue-700 text-lg">-</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-xl">
                            <p class="text-sm text-gray-500 mb-1">Color</p>
                            <p id="detectedColor" class="font-bold text-green-700 text-lg">-</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-xl">
                            <p class="text-sm text-gray-500 mb-1">Theme</p>
                            <p id="detectedTheme" class="font-bold text-purple-700 text-lg">-</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-xl">
                            <p class="text-sm text-gray-500 mb-1">Provider</p>
                            <p id="detectedProvider" class="font-bold text-yellow-700 text-lg">-</p>
                        </div>
                    </div>

                    <!-- Detected Labels -->
                    <div class="mb-6">
                        <h3 class="font-bold text-gray-700 mb-3">Detected Items</h3>
                        <div id="detectedLabels" class="flex flex-wrap gap-2">
                            <!-- Labels will be added here by JS -->
                        </div>
                    </div>

                    <!-- Edit Form -->
                    <form id="wardrobeForm" action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="image" id="formImage">
                        <input type="hidden" name="ai_analysis" id="aiAnalysisData">

                        <div class="space-y-6">
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">
                                    Item Name
                                </label>
                                <input type="text" name="name" id="itemName"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Auto-generated from AI">
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-2">
                                    Category *
                                </label>
                                <select name="category" id="categorySelect" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Category</option>
                                    <option value="top">Top</option>
                                    <option value="bottom">Bottom</option>
                                    <option value="dress">Dress</option>
                                    <option value="outerwear">Outerwear</option>
                                    <option value="shoes">Shoes</option>
                                    <option value="accessory">Accessory</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-2">
                                    Color *
                                </label>
                                <select name="color" id="colorSelect" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Color</option>
                                    <option value="red">Red</option>
                                    <option value="blue">Blue</option>
                                    <option value="green">Green</option>
                                    <option value="black">Black</option>
                                    <option value="white">White</option>
                                    <option value="pink">Pink</option>
                                    <option value="purple">Purple</option>
                                    <option value="yellow">Yellow</option>
                                    <option value="orange">Orange</option>
                                    <option value="gray">Gray</option>
                                    <option value="brown">Brown</option>
                                    <option value="multicolor">Multicolor</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-2">
                                    Theme
                                </label>
                                <select name="theme" id="themeSelect"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Theme</option>
                                    <option value="casual">Casual</option>
                                    <option value="formal">Formal</option>
                                    <option value="party">Party</option>
                                    <option value="sport">Sport</option>
                                    <option value="beach">Beach</option>
                                    <option value="work">Work</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-2">
                                    Notes
                                </label>
                                <textarea name="notes" id="notesTextarea" rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="AI-generated notes..."></textarea>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-wrap gap-4 pt-6 border-t border-gray-200">
                                <button type="submit" 
                                        class="flex-1 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold rounded-xl hover:from-blue-700 hover:to-purple-700 transition shadow">
                                    <i class="fas fa-save mr-2"></i> Save with AI
                                </button>
                                <a href="{{ route('wardrobe.index') }}" 
                                   class="flex-1 py-3 border-2 border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition text-center">
                                    <i class="fas fa-arrow-left mr-2"></i> Back
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Initial State -->
                <div id="initialState" class="text-center py-12">
                    <i class="fas fa-magic text-6xl text-gray-300 mb-6"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-3">
                        Upload a photo to begin AI analysis
                    </h3>
                    <p class="text-gray-500">
                        Google Vision AI will detect clothing type, color, and style automatically.
                    </p>
                </div>
            </div>
        </div>

        <!-- AI Stats -->
        <div class="mt-12 bg-white rounded-2xl shadow-xl p-8">
            <h3 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fab fa-google text-blue-500 mr-2"></i>
                Powered by Google Cloud Vision AI
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 bg-blue-50 rounded-xl">
                    <div class="text-3xl font-bold text-blue-600 mb-2">1000+</div>
                    <p class="text-gray-700">Objects recognized</p>
                    <p class="text-gray-500 text-sm">Clothing, accessories, colors</p>
                </div>
                <div class="p-6 bg-green-50 rounded-xl">
                    <div class="text-3xl font-bold text-green-600 mb-2">95%</div>
                    <p class="text-gray-700">Detection accuracy</p>
                    <p class="text-gray-500 text-sm">High confidence results</p>
                </div>
                <div class="p-6 bg-purple-50 rounded-xl">
                    <div class="text-3xl font-bold text-purple-600 mb-2">< 2s</div>
                    <p class="text-gray-700">Processing time</p>
                    <p class="text-gray-500 text-sm">Fast AI analysis</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for AI Processing -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('imageInput');
    const uploadArea = document.getElementById('uploadArea');
    const preview = document.getElementById('imagePreview');
    const previewImage = document.getElementById('previewImage');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const aiResults = document.getElementById('aiResults');
    const initialState = document.getElementById('initialState');
    const quickUploadBtn = document.getElementById('quickUploadBtn');
    const form = document.getElementById('wardrobeForm');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('border-blue-500', 'bg-blue-50');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('border-blue-500', 'bg-blue-50');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('border-blue-500', 'bg-blue-50');
        
        if (e.dataTransfer.files.length) {
            handleImageUpload(e.dataTransfer.files[0]);
        }
    });

    // File input change
    imageInput.addEventListener('change', (e) => {
        if (e.target.files.length) {
            handleImageUpload(e.target.files[0]);
        }
    });

    // Handle image upload
    function handleImageUpload(file) {
        // Validate
        if (!file.type.match('image.*')) {
            alert('Please upload an image file');
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert('File size should be less than 5MB');
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            preview.classList.remove('hidden');
            initialState.classList.add('hidden');
            
            // Store for form
            document.getElementById('formImage').value = e.target.result;
        };
        reader.readAsDataURL(file);

        // Show loading and analyze
        loadingSpinner.classList.remove('hidden');
        analyzeImage(file);
    }

    // Analyze image with AI
    function analyzeImage(file) {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('_token', csrfToken);

        fetch('{{ route("upload.analyze") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            loadingSpinner.classList.add('hidden');
            
            if (data.success) {
                // Show AI results
                aiResults.classList.remove('hidden');
                updateUIWithAnalysis(data);
                showQuickUploadButton(file, data);
            } else {
                alert('AI analysis failed: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            loadingSpinner.classList.add('hidden');
            console.error('Error:', error);
            alert('Failed to analyze image. Please try again.');
        });
    }

    // Update UI with analysis results
    function updateUIWithAnalysis(data) {
        // Confidence
        const confidence = Math.round(data.confidence * 100);
        document.getElementById('confidenceValue').textContent = confidence + '%';
        document.getElementById('confidenceBar').style.width = confidence + '%';

        // Basic info
        document.getElementById('detectedCategory').textContent = 
            data.category.charAt(0).toUpperCase() + data.category.slice(1);
        document.getElementById('detectedColor').textContent = 
            data.color.charAt(0).toUpperCase() + data.color.slice(1);
        document.getElementById('detectedTheme').textContent = 
            data.theme.charAt(0).toUpperCase() + data.theme.slice(1);
        document.getElementById('detectedProvider').textContent = 
            data.provider === 'google_vision' ? 'Google AI' : 'AI';

        // Form fields
        document.getElementById('itemName').value = 
            data.color.charAt(0).toUpperCase() + data.color.slice(1) + ' ' + 
            data.category.charAt(0).toUpperCase() + data.category.slice(1);
        
        document.getElementById('categorySelect').value = data.category;
        document.getElementById('colorSelect').value = data.color;
        document.getElementById('themeSelect').value = data.theme;
        document.getElementById('notesTextarea').value = 
            'Detected by ' + (data.provider === 'google_vision' ? 'Google Vision AI' : 'AI') + 
            ' with ' + Math.round(data.confidence * 100) + '% confidence.';
        
        // Store analysis data
        document.getElementById('aiAnalysisData').value = JSON.stringify(data.analysis);

        // Labels
        const labelsContainer = document.getElementById('detectedLabels');
        labelsContainer.innerHTML = '';
        
        if (data.labels && data.labels.length > 0) {
            data.labels.forEach(label => {
                const labelEl = document.createElement('span');
                labelEl.className = 'px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm';
                labelEl.textContent = label.description + ' ' + Math.round(label.score * 100) + '%';
                labelsContainer.appendChild(labelEl);
            });
        }
    }

    // Show quick upload button
    function showQuickUploadButton(file, analysis) {
        quickUploadBtn.classList.remove('hidden');
        
        quickUploadBtn.onclick = function() {
            if (!confirm('Auto-add this item with AI-detected details?')) return;
            
            const formData = new FormData();
            formData.append('image', file);
            formData.append('_token', csrfToken);

            fetch('{{ route("upload.quick") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ Item added successfully!');
                    window.location.href = '{{ route("wardrobe.index") }}';
                } else {
                    alert('❌ Failed to add item: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to upload. Please try again.');
            });
        };
    }

    // Form validation
    form.addEventListener('submit', function(e) {
        if (!document.getElementById('categorySelect').value || 
            !document.getElementById('colorSelect').value) {
            e.preventDefault();
            alert('Please fill in required fields (Category and Color)');
        }
    });
});
</script>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endsection