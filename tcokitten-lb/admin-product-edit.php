
<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if user is admin
if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$current_page = 'admin-products';
$pdo = getDBConnection();

// Define upload directory
$upload_dir = 'uploads/products/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_edit = $product_id > 0;

// Get product data if editing
if ($is_edit) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        header('Location: admin-products.php');
        exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $category = sanitize($_POST['category']);
    $price = floatval($_POST['price']);
    $original_price = !empty($_POST['original_price']) ? floatval($_POST['original_price']) : null;
    $stock = intval($_POST['stock']);
    $tags = sanitize($_POST['tags']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // Handle image upload
    $image = $is_edit ? $product['image'] : ''; // Keep existing image by default
    $upload_error = '';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['image']['tmp_name'];
            $file_name = $_FILES['image']['name'];
            $file_size = $_FILES['image']['size'];
            $file_type = $_FILES['image']['type'];
            
            // Get file extension
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Valid extensions
            $valid_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            // Check extension
            if (in_array($file_ext, $valid_extensions)) {
                // Check file size (max 5MB)
                if ($file_size <= 5242880) { // 5MB in bytes
                    // Generate unique filename
                    $unique_name = uniqid('product_', true) . '_' . time() . '.' . $file_ext;
                    $upload_path = $upload_dir . $unique_name;
                    
                    // Move uploaded file
                    if (move_uploaded_file($file_tmp, $upload_path)) {
                        // Delete old image if it exists and is a local file
                        if ($is_edit && !empty($product['image']) && file_exists($product['image'])) {
                            // Check if it's a local file (not a URL)
                            if (!filter_var($product['image'], FILTER_VALIDATE_URL) && strpos($product['image'], 'http') !== 0) {
                                unlink($product['image']);
                                // Also delete thumbnail if exists
                                $thumb_path = dirname($product['image']) . '/thumbs/' . basename($product['image']);
                                if (file_exists($thumb_path)) {
                                    unlink($thumb_path);
                                }
                            }
                        }
                        
                        $image = $upload_path;
                        
                        // Create thumbnail
                        createThumbnail($upload_path, $upload_dir . 'thumbs/' . $unique_name, 300, 300);
                    } else {
                        $upload_error = 'Failed to upload image.';
                    }
                } else {
                    $upload_error = 'File size must be less than 5MB.';
                }
            } else {
                $upload_error = 'Only JPG, JPEG, PNG, GIF, and WebP files are allowed.';
            }
        } else {
            switch ($_FILES['image']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $upload_error = 'File size exceeds limit.';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $upload_error = 'File upload was partial.';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $upload_error = 'No temporary directory.';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $upload_error = 'Cannot write to disk.';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $upload_error = 'File upload stopped by extension.';
                    break;
                default:
                    $upload_error = 'Unknown upload error.';
            }
        }
    }
    
    // Handle image URL if no file uploaded
    if (empty($_FILES['image']['tmp_name']) && !empty($_POST['image_url'])) {
        $new_image_url = sanitize($_POST['image_url']);
        // Only update if URL is different from current image
        if ($new_image_url !== $image) {
            // Delete old image if it exists and is a local file
            if ($is_edit && !empty($product['image']) && file_exists($product['image'])) {
                // Check if it's a local file (not a URL)
                if (!filter_var($product['image'], FILTER_VALIDATE_URL) && strpos($product['image'], 'http') !== 0) {
                    unlink($product['image']);
                    // Also delete thumbnail if exists
                    $thumb_path = dirname($product['image']) . '/thumbs/' . basename($product['image']);
                    if (file_exists($thumb_path)) {
                        unlink($thumb_path);
                    }
                }
            }
            $image = $new_image_url;
        }
    }
    
    // Validation
    $errors = [];
    if (empty($name)) $errors[] = "Product name is required";
    if (empty($description)) $errors[] = "Description is required";
    if ($price <= 0) $errors[] = "Price must be greater than 0";
    if ($stock < 0) $errors[] = "Stock cannot be negative";
    if (!empty($upload_error)) $errors[] = "Image upload: " . $upload_error;
    
    if (empty($errors)) {
        if ($is_edit) {
            // Update existing product
            $stmt = $pdo->prepare("
                UPDATE products SET 
                name = ?, description = ?, category = ?, price = ?, original_price = ?, 
                stock = ?, tags = ?, featured = ?, image = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $success = $stmt->execute([
                $name, $description, $category, $price, $original_price,
                $stock, $tags, $featured, $image, $product_id
            ]);
            $message = "Product updated successfully!";
        } else {
            // Insert new product
            $stmt = $pdo->prepare("
                INSERT INTO products (name, description, category, price, original_price, 
                stock, tags, featured, image, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $success = $stmt->execute([
                $name, $description, $category, $price, $original_price,
                $stock, $tags, $featured, $image
            ]);
            $product_id = $pdo->lastInsertId();
            $message = "Product added successfully!";
        }
        
        if ($success) {
            $_SESSION['success_message'] = $message;
            header("Location: admin-products.php");
            exit;
        } else {
            $errors[] = "Database error. Please try again.";
        }
    }
}

// Function to create thumbnail
function createThumbnail($source_path, $dest_path, $width, $height) {
    $dir = dirname($dest_path);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    
    $info = getimagesize($source_path);
    if (!$info) return false;
    
    list($original_width, $original_height, $type) = $info;
    
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($source_path);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($source_path);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($source_path);
            break;
        case IMAGETYPE_WEBP:
            $source = imagecreatefromwebp($source_path);
            break;
        default:
            return false;
    }
    
    // Calculate aspect ratio
    $source_aspect = $original_width / $original_height;
    $thumb_aspect = $width / $height;
    
    if ($source_aspect > $thumb_aspect) {
        // Source is wider
        $new_height = $height;
        $new_width = floor($height * $source_aspect);
    } else {
        // Source is taller or square
        $new_width = $width;
        $new_height = floor($width / $source_aspect);
    }
    
    // Create thumbnail image
    $thumb = imagecreatetruecolor($new_width, $new_height);
    
    // Preserve transparency for PNG and GIF
    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
        imagecolortransparent($thumb, imagecolorallocatealpha($thumb, 0, 0, 0, 127));
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
    }
    
    // Resize image
    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
    
    // Save image
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($thumb, $dest_path, 85);
            break;
        case IMAGETYPE_PNG:
            imagepng($thumb, $dest_path, 8);
            break;
        case IMAGETYPE_GIF:
            imagegif($thumb, $dest_path);
            break;
        case IMAGETYPE_WEBP:
            imagewebp($thumb, $dest_path, 85);
            break;
    }
    
    // Free memory
    imagedestroy($source);
    imagedestroy($thumb);
    
    return true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? 'Edit' : 'Add'; ?> Product - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include 'admin-sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Top Bar -->
            <div class="bg-white shadow">
                <div class="flex justify-between items-center p-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            <?php echo $is_edit ? 'Edit Product' : 'Add New Product'; ?>
                        </h1>
                        <p class="text-gray-600"><?php echo $is_edit ? 'Update product information' : 'Add a new product to your catalog'; ?></p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="admin-products.php" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Products
                        </a>
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-indigo-600"></i>
                            </div>
                            <span class="font-medium"><?php echo $_SESSION['user_name'] ?? 'Administrator'; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <?php if (!empty($errors)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 m-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Product Form -->
            <div class="p-6">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white rounded-xl shadow p-8">
                        <form method="POST" class="space-y-6" enctype="multipart/form-data">
                            <!-- Basic Information -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Product Name *
                                        </label>
                                        <input type="text" name="name" required 
                                               value="<?php echo $is_edit ? htmlspecialchars($product['name']) : ''; ?>"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Category *
                                        </label>
                                        <select name="category" required 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                            <option value="">Select Category</option>
                                            <option value="food" <?php echo $is_edit && $product['category'] == 'food' ? 'selected' : ''; ?>>Food</option>
                                            <option value="toys" <?php echo $is_edit && $product['category'] == 'toys' ? 'selected' : ''; ?>>Toys</option>
                                            <option value="accessories" <?php echo $is_edit && $product['category'] == 'accessories' ? 'selected' : ''; ?>>Accessories</option>
                                            <option value="health" <?php echo $is_edit && $product['category'] == 'health' ? 'selected' : ''; ?>>Health</option>
                                            <option value="litter" <?php echo $is_edit && $product['category'] == 'litter' ? 'selected' : ''; ?>>Litter</option>
                                            <option value="travel" <?php echo $is_edit && $product['category'] == 'travel' ? 'selected' : ''; ?>>Travel</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Description *
                                </label>
                                <textarea name="description" rows="4" required 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"><?php echo $is_edit ? htmlspecialchars($product['description']) : ''; ?></textarea>
                            </div>

                            <!-- Pricing -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Pricing & Inventory</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Price *
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500">$</span>
                                            </div>
                                            <input type="number" name="price" step="0.01" min="0.01" required 
                                                   value="<?php echo $is_edit ? $product['price'] : ''; ?>"
                                                   class="pl-7 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Original Price (for discount)
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500">$</span>
                                            </div>
                                            <input type="number" name="original_price" step="0.01" min="0"
                                                   value="<?php echo $is_edit ? $product['original_price'] : ''; ?>"
                                                   class="pl-7 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Stock Quantity *
                                        </label>
                                        <input type="number" name="stock" min="0" required 
                                               value="<?php echo $is_edit ? $product['stock'] : '0'; ?>"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Image & Tags -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Media & Tags</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Product Image
                                        </label>
                                        <div class="space-y-4">
                                            <!-- Current Image -->
                                            <?php if ($is_edit && !empty($product['image'])): ?>
                                                <div class="mb-4">
                                                    <p class="text-sm text-gray-600 mb-2">Current Image:</p>
                                                    <div class="flex items-center space-x-4">
                                                        <img src="<?php echo $product['image']; ?>" 
                                                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                             class="w-24 h-24 object-cover rounded-lg border border-gray-300">
                                                        <div>
                                                            <p class="text-sm text-gray-600">
                                                                <?php 
                                                                if (filter_var($product['image'], FILTER_VALIDATE_URL)) {
                                                                    echo 'External URL';
                                                                } else {
                                                                    echo 'Uploaded file';
                                                                }
                                                                ?>
                                                            </p>
                                                            <button type="button" onclick="removeCurrentImage()" class="text-sm text-red-600 hover:text-red-800 mt-1">
                                                                <i class="fas fa-trash mr-1"></i>Remove current image
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div>
                                                <label class="block text-sm text-gray-600 mb-2">Upload New Image:</label>
                                                <div class="flex items-center space-x-4">
                                                    <label class="cursor-pointer bg-indigo-50 border border-indigo-200 text-indigo-700 px-4 py-3 rounded-lg hover:bg-indigo-100 transition-colors">
                                                        <i class="fas fa-cloud-upload-alt mr-2"></i>
                                                        Choose File
                                                        <input type="file" name="image" accept="image/*" class="hidden" id="imageUpload">
                                                    </label>
                                                    <span id="fileName" class="text-sm text-gray-500">No file chosen</span>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1">Max size: 5MB. Supported: JPG, PNG, GIF, WebP</p>
                                            </div>
                                            
                                            <div class="pt-2 border-t">
                                                <label class="block text-sm text-gray-600 mb-2">Or use Image URL:</label>
                                                <input type="url" name="image_url" 
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                       placeholder="https://example.com/image.jpg"
                                                       value="<?php echo $is_edit && filter_var($product['image'], FILTER_VALIDATE_URL) ? $product['image'] : ''; ?>">
                                                <p class="text-xs text-gray-500 mt-1">If no file is uploaded, you can provide an image URL</p>
                                            </div>
                                        </div>
                                        
                                        <!-- New Image Preview -->
                                        <div id="imagePreview" class="mt-4 hidden">
                                            <h4 class="text-sm font-medium text-gray-700 mb-2">New Image Preview:</h4>
                                            <img id="previewImage" src="" alt="Preview" 
                                                 class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                                            <div class="mt-2">
                                                <button type="button" onclick="removeNewImage()" class="text-sm text-red-600 hover:text-red-800">
                                                    <i class="fas fa-times mr-1"></i>Remove new image
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Tags
                                        </label>
                                        <input type="text" name="tags" 
                                               value="<?php echo $is_edit ? htmlspecialchars($product['tags']) : ''; ?>"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                               placeholder="best-seller, premium, organic">
                                        <p class="text-sm text-gray-500 mt-1">Separate tags with commas</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Settings -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Additional Settings</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="featured" id="featured" 
                                               <?php echo $is_edit && $product['featured'] ? 'checked' : ''; ?>
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="featured" class="ml-2 block text-sm text-gray-700">
                                            Mark as Featured Product
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden field for current image removal -->
                            <input type="hidden" id="removeCurrentImage" name="remove_current_image" value="0">

                            <!-- Form Actions -->
                            <div class="flex justify-end space-x-4 pt-6 border-t">
                                <a href="admin-products.php" 
                                   class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                    Cancel
                                </a>
                                <button type="submit" 
                                        class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                    <?php echo $is_edit ? 'Update Product' : 'Add Product'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Quick Tips -->
                    <div class="mt-6 bg-blue-50 border border-blue-100 rounded-lg p-6">
                        <h3 class="font-semibold text-blue-800 mb-3">Quick Tips</h3>
                        <ul class="text-sm text-blue-700 space-y-2">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-blue-500 mt-1 mr-2"></i>
                                <span>Use descriptive names that customers can easily understand</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-blue-500 mt-1 mr-2"></i>
                                <span>Include key features and benefits in the description</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-blue-500 mt-1 mr-2"></i>
                                <span>Add relevant tags to help with search and filtering</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-blue-500 mt-1 mr-2"></i>
                                <span>Featured products will appear on the homepage</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-blue-500 mt-1 mr-2"></i>
                                <span>Upload high-quality product images for better conversion</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-blue-500 mt-1 mr-2"></i>
                                <span>When replacing an image, the old uploaded file will be deleted</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // File upload and preview functionality
        const imageUpload = document.getElementById('imageUpload');
        const fileName = document.getElementById('fileName');
        const previewContainer = document.getElementById('imagePreview');
        const previewImage = document.getElementById('previewImage');
        const imageUrlInput = document.querySelector('input[name="image_url"]');
        const removeCurrentImageField = document.getElementById('removeCurrentImage');
        
        // Handle file upload
        imageUpload.addEventListener('change', function() {
            const file = this.files[0];
            
            if (file) {
                fileName.textContent = file.name;
                
                // Validate file size (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size must be less than 5MB');
                    this.value = '';
                    fileName.textContent = 'No file chosen';
                    previewContainer.classList.add('hidden');
                    return;
                }
                
                // Validate file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    alert('Only JPG, PNG, GIF, and WebP files are allowed');
                    this.value = '';
                    fileName.textContent = 'No file chosen';
                    previewContainer.classList.add('hidden');
                    return;
                }
                
                // Preview image
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
                
                // Clear URL input
                imageUrlInput.value = '';
            } else {
                fileName.textContent = 'No file chosen';
                previewContainer.classList.add('hidden');
            }
        });
        
        // Handle URL input
        imageUrlInput.addEventListener('input', function() {
            const url = this.value.trim();
            
            if (url && isValidUrl(url)) {
                previewImage.src = url;
                previewContainer.classList.remove('hidden');
                
                // Handle image loading errors
                previewImage.onerror = function() {
                    previewContainer.classList.add('hidden');
                    alert('Could not load image from the provided URL. Please check the URL and try again.');
                };
                
                previewImage.onload = function() {
                    previewContainer.classList.remove('hidden');
                };
                
                // Clear file input
                imageUpload.value = '';
                fileName.textContent = 'No file chosen';
            } else if (!url) {
                previewContainer.classList.add('hidden');
            }
        });
        
        // Remove current image
        function removeCurrentImage() {
            if (confirm('Are you sure you want to remove the current image?')) {
                removeCurrentImageField.value = '1';
                // Hide current image display (you might need to reload the page or update UI)
                alert('Current image will be removed when you save the product.');
            }
        }
        
        // Remove new image
        function removeNewImage() {
            imageUpload.value = '';
            imageUrlInput.value = '';
            fileName.textContent = 'No file chosen';
            previewContainer.classList.add('hidden');
            previewImage.src = '';
        }
        
        function isValidUrl(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const price = document.querySelector('input[name="price"]').value;
            const stock = document.querySelector('input[name="stock"]').value;
            
            // Price validation
            if (parseFloat(price) <= 0) {
                e.preventDefault();
                alert('Price must be greater than 0');
                return false;
            }
            
            // Stock validation
            if (parseInt(stock) < 0) {
                e.preventDefault();
                alert('Stock quantity cannot be negative');
                return false;
            }
            
            // Validate original price if provided
            const originalPrice = document.querySelector('input[name="original_price"]').value;
            if (originalPrice && parseFloat(originalPrice) <= parseFloat(price)) {
                e.preventDefault();
                alert('Original price must be greater than current price for a discount');
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>