<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to get global max images setting
function getGlobalMaxImages() {
    $defaultMax = 5;
    $configFile = $_SERVER['DOCUMENT_ROOT'] . '/hanouty/config/global_settings.json';
    
    // Try to read from config file first
    if (file_exists($configFile)) {
        $config = json_decode(file_get_contents($configFile), true);
        if (isset($config['global_max_images']) && intval($config['global_max_images']) > 0) {
            $globalMax = intval($config['global_max_images']);
            // Update session to keep it in sync
            $_SESSION['global_max_images'] = $globalMax;
            return $globalMax;
        }
    }
    
    // Fallback to session
    if (isset($_SESSION['global_max_images']) && intval($_SESSION['global_max_images']) > 0) {
        return intval($_SESSION['global_max_images']);
    }
    
    return $defaultMax;
}

// PHP validation for all fields
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? '';
    $category = $_POST['category'] ?? '';
    $allowedCategories = [
        'Electronics','Clothing','Home & Garden','Sports','Books','Toys','Automotive','Health & Beauty','Other'
    ];
    $uploadedCount = isset($_FILES['images']['name']) ? count(array_filter($_FILES['images']['name'])) : 0;
    $existingCount = isset($_POST['existing_images']) ? count($_POST['existing_images']) : 0;
    $totalCount = $uploadedCount + $existingCount;

    // Fetch dynamic max image limit
    require_once $_SERVER['DOCUMENT_ROOT'] . '/hanouty/model/Product.php';
    $maxProductImages = getGlobalMaxImages(); // Use the function to get current setting
    
    // If editing an existing product, use product-specific limit
    if (isset($_GET['product_id'])) {
        $productModel = new Product();
        $product = $productModel->getById((int)$_GET['product_id']);
        if ($product && isset($product['max_product_images']) && $product['max_product_images'] > 0) {
            $maxProductImages = (int)$product['max_product_images'];
        }
    }
    
    // Debug: Log the validation values
    error_log("POST validation - Max images: " . $maxProductImages);
    error_log("POST validation - Total images: " . $totalCount);
    error_log("POST validation - Uploaded: " . $uploadedCount . ", Existing: " . $existingCount);

    // Title validation
    if ($title === '') {
        $errors['title'] = 'Title is required.';
    } elseif (mb_strlen($title) < 3 || mb_strlen($title) > 100) {
        $errors['title'] = 'Title must be between 3 and 100 characters.';
    }
    
    // Description validation
    if ($description === '') {
        $errors['description'] = 'Description is required.';
    } elseif (mb_strlen($description) < 10 || mb_strlen($description) > 1000) {
        $errors['description'] = 'Description must be between 10 and 1000 characters.';
    }
    
    // Price validation
    if ($price === '' || !is_numeric($price)) {
        $errors['price'] = 'Price is required and must be a number.';
    } elseif ($price < 0) {
        $errors['price'] = 'Price must be a positive number.';
    }
    
    // Category validation
    if ($category === '' || !in_array($category, $allowedCategories)) {
        $errors['category'] = 'Please select a valid category.';
    }
    
    // Images validation - STRICT validation that blocks submission
    if ($totalCount > $maxProductImages) {
        $errors['images'] = 'You have selected more than ' . $maxProductImages . ' images. Please select up to ' . $maxProductImages . ' images only.';
        $addProductError = $errors['images'];
        error_log("BLOCKING SUBMISSION: Total images ($totalCount) exceeds limit ($maxProductImages)");
    }
    
    // Check if user is logged in and is a supplier
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
        $errors['auth'] = 'You must be logged in as a supplier to add products.';
    }
    
    // If errors exist, show first error as alert and DO NOT process product creation
    if (!empty($errors)) {
        $addProductError = reset($errors);
        error_log("Form has errors, not proceeding with product creation: " . json_encode($errors));
    } else {
        // Only proceed if NO errors exist - ACTUAL PRODUCT CREATION
        error_log("ALLOWING SUBMISSION: Total images ($totalCount) within limit ($maxProductImages)");
        
        try {
            // Initialize product model
            $productModel = new Product();
            
            // Handle image uploads
            $imageFilenames = [];
            
            // Process uploaded files
            if (!empty($_FILES['images']['name'][0])) {
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/hanouty/uploads/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                foreach ($_FILES['images']['name'] as $key => $filename) {
                    if (!empty($filename)) {
                        $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        
                        if (in_array($fileExtension, $allowedExtensions)) {
                            $newFilename = $_SESSION['user_id'] . '_' . time() . '_' . $key . '.' . $fileExtension;
                            $targetPath = $uploadDir . $newFilename;
                            
                            if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $targetPath)) {
                                $imageFilenames[] = 'uploads/products/' . $newFilename;
                            }
                        }
                    }
                }
            }
            
            // Add existing images from back office
            if (!empty($_POST['existing_images'])) {
                foreach ($_POST['existing_images'] as $existingImage) {
                    $imageFilenames[] = 'uploads/products/' . $existingImage;
                }
            }
            
            // Create product data array
            $productData = [
                'title' => $title,
                'description' => $description,
                'price' => (float)$price,
                'category' => $category,
                'supplier_id' => $_SESSION['user_id'],
                'images' => json_encode($imageFilenames),
                'max_product_images' => $maxProductImages,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // Add featured page and spot if provided
            if (!empty($_POST['featured_page'])) {
                $productData['featured_page'] = (int)$_POST['featured_page'];
            }
            if (!empty($_POST['spot'])) {
                $productData['spot'] = (int)$_POST['spot'];
            }
            
            // Insert product into database
            $productId = $productModel->createProduct($productData);
            
            if ($productId) {
                $addProductSuccess = 'Product created successfully! Product ID: ' . $productId;
                error_log("Product created successfully with ID: " . $productId);
                
                // Clear form data after successful submission
                $_POST = [];
                
                // Optionally redirect to avoid resubmission
                // header('Location: router.php?action=add-product&success=1');
                // exit();
            } else {
                $addProductError = 'Failed to create product. Please try again.';
                error_log("Failed to create product in database");
            }
            
        } catch (Exception $e) {
            $addProductError = 'An error occurred while creating the product: ' . $e->getMessage();
            error_log("Exception during product creation: " . $e->getMessage());
        }
    }
}

// Always define max image limit for use in HTML
require_once $_SERVER['DOCUMENT_ROOT'] . '/hanouty/model/Product.php';
$maxProductImages = getGlobalMaxImages(); // Use the function consistently

// If editing an existing product, use product-specific limit
if (isset($_GET['product_id'])) {
    $productModel = new Product();
    $product = $productModel->getById((int)$_GET['product_id']);
    if ($product && isset($product['max_product_images']) && $product['max_product_images'] > 0) {
        $maxProductImages = (int)$product['max_product_images'];
    }
}

// Debug: Log the current max images setting
error_log("Current max product images: " . $maxProductImages);
error_log("Config file exists: " . (file_exists($_SERVER['DOCUMENT_ROOT'] . '/hanouty/config/global_settings.json') ? 'YES' : 'NO'));

// Load supplier's uploaded images for selection
$supplierImages = [];
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'supplier') {
    $userId = $_SESSION['user_id'];
    $dir = __DIR__ . '/../../../uploads/products/';
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if (preg_match('/^' . $userId . '_/', $file)) {
                $supplierImages[] = $file;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Add new product - Hanouty" />
    <meta name="author" content="Hanouty" />
    <title>Add Product - Hanouty</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .form-header {
            background: linear-gradient(135deg, #198754 0%, #20c997 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .form-body {
            padding: 2rem;
        }
        .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1rem;
        }
        .image-preview img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 0.5rem;
            border: 2px solid #dee2e6;
            transition: all 0.2s ease-in-out;
        }
        .image-preview .btn-danger {
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
        }
        .image-preview .position-relative:hover .btn-danger {
            opacity: 1;
        }
        .image-preview .position-relative:hover img {
            filter: brightness(0.9);
        }
        .file-input-wrapper {
            position: relative;
            display: inline-block;
            cursor: pointer;
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        .file-input-wrapper:hover {
            border-color: #198754;
            background: #e8f5e8;
        }
        .file-input-wrapper input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        .btn-back {
            position: absolute;
            top: 1rem;
            left: 1rem;
            z-index: 10;
        }
        .submit-btn-disabled {
            opacity: 0.6;
            pointer-events: none;
        }
        .form-error {
            border-color: #dc3545 !important;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .config-debug {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 0.375rem;
            padding: 0.75rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body class="bg-light">
<!-- Navigation-->
<nav class="navbar navbar-expand-lg navbar-light" style="background: linear-gradient(135deg, #198754 0%, #20c997 100%); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
    <div class="container px-4 px-lg-5">
        <a class="navbar-brand text-white" href="/hanouty/view/front_office/router.php"><strong>Hanouty</strong></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                <li class="nav-item"><a class="nav-link text-white" aria-current="page" href="/hanouty/view/front_office/router.php">Home</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="/hanouty/view/front_office/router.php?action=common-products">Common Products</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="/hanouty/view/front_office/router.php?action=flash-sale">Flash Sale</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="nav-link text-white" href="/hanouty/view/front_office/router.php?action=profile">Profile</a></li>
                <?php endif; ?>
            </ul>
            <!-- Search Form -->
            <form class="d-flex me-3" method="GET" action="/hanouty/view/front_office/router.php">
                <input class="form-control me-2" type="search" name="search" placeholder="Search products..." value="<?= isset($searchTerm) ? htmlspecialchars($searchTerm) : '' ?>">
                <button class="btn btn-outline-dark" type="submit">Search</button>
            </form>
            <!-- User Menu -->
            <div class="d-flex">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-dark dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi-person-fill me-1"></i>
                            <?= htmlspecialchars($_SESSION['user_name']) ?>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <?php if ($_SESSION['user_role'] === 'supplier'): ?>
                                <li><a class="dropdown-item" href="../back_office/index.php">Dashboard</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/hanouty/view/front_office/router.php?action=logout">Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <button class="btn btn-outline-dark" type="button" onclick="showLoginModal()">
                        <i class="bi-person-fill me-1"></i>
                        Login
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

    <!-- Back Button -->
    <div class="container mt-3">
        <a href="router.php" class="btn btn-outline-dark">
            <i class="bi-arrow-left me-1"></i>
            Back to Home
        </a>
    </div>

    <div class="container py-5">
        <div class="form-container">
            <div class="form-header">
                <h1 class="mb-0">
                    <i class="bi-plus-circle me-2"></i>
                    Add New Product
                </h1>
                <p class="mb-0 mt-2">Share your products with customers</p>
            </div>
            
            <div class="form-body">
                <!-- Debug Info -->
                <div class="config-debug">
                    <strong>Current Configuration:</strong><br>
                    Max Images Allowed: <?= $maxProductImages ?><br>
                </div>
                
                <?php if (isset($addProductSuccess)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi-check-circle me-2"></i>
                        <?= htmlspecialchars($addProductSuccess) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($addProductError)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi-exclamation-triangle me-2"></i>
                        <?= htmlspecialchars($addProductError) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="router.php?action=add-product<?php if (isset($_GET['featured_page'])) echo '&featured_page=' . (int)$_GET['featured_page']; if (isset($_GET['spot'])) echo '&spot=' . (int)$_GET['spot']; ?>" enctype="multipart/form-data" id="productForm">
                    <input type="hidden" name="featured_page" value="<?= htmlspecialchars($_GET['featured_page'] ?? ($_POST['featured_page'] ?? '')) ?>">
                    <input type="hidden" name="spot" value="<?= htmlspecialchars($_GET['spot'] ?? ($_POST['spot'] ?? '')) ?>">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Product Title -->
                            <div class="mb-3">
                                <label for="title" class="form-label">
                                    <i class="bi-tag me-1"></i>
                                    Product Title *
                                </label>
                                <input class="form-control <?= isset($errors['title']) ? 'form-error' : '' ?>" id="title" name="title" placeholder="Enter product title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
                                <?php if (isset($errors['title'])): ?>
                                    <div class="error-message"><?= htmlspecialchars($errors['title']) ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Product Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    <i class="bi-text-paragraph me-1"></i>
                                    Description *
                                </label>
                                <textarea class="form-control <?= isset($errors['description']) ? 'form-error' : '' ?>" id="description" name="description" rows="4" placeholder="Describe your product in detail"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                                <?php if (isset($errors['description'])): ?>
                                    <div class="error-message"><?= htmlspecialchars($errors['description']) ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Price and Category -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">
                                            <i class="bi-cash me-1"></i>
                                            Price *
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">DT</span>
                                            <input class="form-control <?= isset($errors['price']) ? 'form-error' : '' ?>" id="price" name="price" placeholder="0.00" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
                                        </div>
                                        <?php if (isset($errors['price'])): ?>
                                            <div class="error-message"><?= htmlspecialchars($errors['price']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category" class="form-label">
                                            <i class="bi-collection me-1"></i>
                                            Category *
                                        </label>
                                        <select class="form-select <?= isset($errors['category']) ? 'form-error' : '' ?>" id="category" name="category">
                                            <option value="">Select category</option>
                                            <option value="Electronics" <?= ($_POST['category'] ?? '') === 'Electronics' ? 'selected' : '' ?>>Electronics</option>
                                            <option value="Clothing" <?= ($_POST['category'] ?? '') === 'Clothing' ? 'selected' : '' ?>>Clothing</option>
                                            <option value="Home & Garden" <?= ($_POST['category'] ?? '') === 'Home & Garden' ? 'selected' : '' ?>>Home & Garden</option>
                                            <option value="Sports" <?= ($_POST['category'] ?? '') === 'Sports' ? 'selected' : '' ?>>Sports</option>
                                            <option value="Books" <?= ($_POST['category'] ?? '') === 'Books' ? 'selected' : '' ?>>Books</option>
                                            <option value="Toys" <?= ($_POST['category'] ?? '') === 'Toys' ? 'selected' : '' ?>>Toys</option>
                                            <option value="Automotive" <?= ($_POST['category'] ?? '') === 'Automotive' ? 'selected' : '' ?>>Automotive</option>
                                            <option value="Health & Beauty" <?= ($_POST['category'] ?? '') === 'Health & Beauty' ? 'selected' : '' ?>>Health & Beauty</option>
                                            <option value="Other" <?= ($_POST['category'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                                        </select>
                                        <?php if (isset($errors['category'])): ?>
                                            <div class="error-message"><?= htmlspecialchars($errors['category']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <!-- Product Images -->
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi-images me-1"></i>
                                    Product Images 
                                </label>
                                <div id="image-limit-warning" class="mt-1" style="display:none;"></div>
                                <div class="file-input-wrapper">
                                    <input type="file" id="images" name="images[]" multiple accept="image/*" onchange="previewImages(this)">
                                    <div>
                                        <i class="bi-cloud-upload fs-1 text-muted"></i>
                                        <p class="mb-0 mt-2">Click to upload images</p>
                                        <small class="text-muted">JPG, PNG, GIF, WebP (max <?= $maxProductImages ?> images)</small>
                                    </div>
                                </div>
                                <div id="imagePreview" class="image-preview"></div>
                                <?php if (isset($errors['images'])): ?>
                                    <div class="error-message"><?= htmlspecialchars($errors['images']) ?></div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($supplierImages)): ?>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi-images me-1"></i>
                                    Select from My Uploaded Images (Back Office)
                                </label>
                                <div class="row">
                                    <?php foreach ($supplierImages as $img): ?>
                                        <div class="col-3 mb-2 text-center">
                                            <label style="cursor:pointer;">
                                                <input type="checkbox" name="existing_images[]" value="<?= htmlspecialchars($img) ?>" style="margin-bottom:5px;" class="existing-image-checkbox">
                                                <img src="/hanouty/uploads/products/<?= htmlspecialchars($img) ?>" class="img-fluid rounded" style="max-height:80px;">
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-lg px-5" id="submitBtn">
                            <i class="bi-plus-circle me-2"></i>
                            Add Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- Footer-->
<footer class="py-5 bg-dark">
    <div class="container">
        <p class="m-0 text-center text-white">Copyright &copy; Hanouty 2025</p>
    </div>
</footer>
    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Pass PHP maxProductImages to JS
    window.maxProductImages = <?= json_encode($maxProductImages) ?>;
    
    console.log('Max images allowed:', window.maxProductImages);
    
    // Function to check image limit and disable/enable submit button
    function updateImageSelectionLimit() {
        const maxImages = typeof window.maxProductImages !== 'undefined' ? window.maxProductImages : 5;
        const fileInput = document.getElementById('images');
        const checkboxes = document.querySelectorAll('.existing-image-checkbox');
        const warningDiv = document.getElementById('image-limit-warning');
        const submitBtn = document.getElementById('submitBtn');
        const countDisplay = document.getElementById('image-count-display');
        
        let checkedCount = 0;
        checkboxes.forEach(cb => { if (cb.checked) checkedCount++; });
        const filesCount = fileInput.files ? fileInput.files.length : 0;
        const totalSelected = checkedCount + filesCount;

        // Update count display
        countDisplay.textContent = `Selected: ${totalSelected} of ${maxImages} images`;
        countDisplay.className = totalSelected > maxImages ? 'text-danger fw-bold' : 'text-muted';

        if (totalSelected > maxImages) {
            warningDiv.style.display = 'block';
            warningDiv.className = 'text-danger fw-bold mt-1 alert alert-danger';
            warningDiv.innerHTML = `<i class="bi-exclamation-triangle me-2"></i>You have selected <strong>${totalSelected - maxImages}</strong> image(s) more than the maximum allowed (<strong>${maxImages}</strong>). Please remove some images to continue.`;
            
            // Disable submit button with strong visual indication
            submitBtn.classList.add('submit-btn-disabled');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi-x-circle me-2"></i>Too Many Images - Cannot Submit';
            
        } else {
            warningDiv.style.display = 'none';
            warningDiv.innerHTML = '';
            
            // Enable submit button
            submitBtn.classList.remove('submit-btn-disabled');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi-plus-circle me-2"></i>Add Product';
        }
        
        console.log(`Images selected: ${totalSelected}/${maxImages}`);
    }

    function previewImages(input) {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';
        const maxFiles = typeof window.maxProductImages !== 'undefined' ? window.maxProductImages : 5;
        
        let filesArray = input.files ? Array.from(input.files) : [];
        const dataTransfer = new DataTransfer();

        // Show previews for all selected images (not limited to maxFiles for preview)
        filesArray.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'position-relative d-inline-block me-2 mb-2';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = `Preview ${index + 1}`;
                    if (index >= maxFiles) {
                        img.style.border = '2px solid #dc3545';
                        img.title = 'This image exceeds the limit and will not be processed';
                    }
                    
                    const deleteBtn = document.createElement('button');
                    deleteBtn.type = 'button';
                    deleteBtn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle';
                    deleteBtn.style.width = '24px';
                    deleteBtn.style.height = '24px';
                    deleteBtn.style.padding = '0';
                    deleteBtn.style.transform = 'translate(50%, -50%)';
                    deleteBtn.innerHTML = '×';
                    
                    deleteBtn.onclick = function() {
                        const newDataTransfer = new DataTransfer();
                        const fileInput = document.getElementById('images');
                        
                        Array.from(fileInput.files).forEach((f, i) => {
                            if (i !== index) newDataTransfer.items.add(f);
                        });
                        
                        fileInput.files = newDataTransfer.files;
                        wrapper.remove();
                        updateImageSelectionLimit();
                    };
                    
                    wrapper.appendChild(img);
                    wrapper.appendChild(deleteBtn);
                    preview.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
                dataTransfer.items.add(file);
            }
        });

        // Update button state and warning
        updateImageSelectionLimit();
    }

    // Prevent form submission if image limit exceeded
    function validateFormSubmission(event) {
        const maxImages = typeof window.maxProductImages !== 'undefined' ? window.maxProductImages : 5;
        const fileInput = document.getElementById('images');
        const checkboxes = document.querySelectorAll('.existing-image-checkbox');
        
        let checkedCount = 0;
        checkboxes.forEach(cb => { if (cb.checked) checkedCount++; });
        const filesCount = fileInput.files ? fileInput.files.length : 0;
        const totalSelected = checkedCount + filesCount;

        if (totalSelected > maxImages) {
            event.preventDefault();
            alert(`❌ SUBMISSION BLOCKED!\n\nYou cannot submit the form with more than ${maxImages} images.\nYou currently have ${totalSelected} images selected.\n\nPlease remove ${totalSelected - maxImages} image(s) and try again.`);
            return false;
        }
        
        if (totalSelected === 0) {
            event.preventDefault();
            alert('Please select at least one image for your product.');
            return false;
        }
        
        console.log('Form submission allowed - image count within limits');
        return true;
    }

    // Listen for changes on checkboxes and file input
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('images');
        const checkboxes = document.querySelectorAll('.existing-image-checkbox');
        const form = document.getElementById('productForm');
        
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateImageSelectionLimit();
            });
        });
        
        fileInput.addEventListener('change', function() {
            previewImages(fileInput);
        });
        
        // Add form submission validation
        form.addEventListener('submit', validateFormSubmission);
        
        // Initial update
        updateImageSelectionLimit();
        
        console.log('Form validation initialized with max images:', window.maxProductImages);
    });
    </script>
</body>
</html>