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
    </style>
</head>
<body class="bg-light">
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
                
                <form method="POST" action="router.php?action=add-product" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Product Title -->
                            <div class="mb-3">
                                <label for="title" class="form-label">
                                    <i class="bi-tag me-1"></i>
                                    Product Title *
                                </label>
                                <input type="text" class="form-control" id="title" name="title" required 
                                       placeholder="Enter product title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
                            </div>
                            
                            <!-- Product Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    <i class="bi-text-paragraph me-1"></i>
                                    Description *
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="4" required 
                                          placeholder="Describe your product in detail"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                            </div>
                            
                            <!-- Price and Category -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">
                                            <i class="bi-currency-dollar me-1"></i>
                                            Price *
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required 
                                                   placeholder="0.00" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category" class="form-label">
                                            <i class="bi-collection me-1"></i>
                                            Category
                                        </label>
                                        <select class="form-select" id="category" name="category">
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
                                <div class="file-input-wrapper">
                                    <input type="file" id="images" name="images[]" multiple accept="image/*" onchange="previewImages(this)">
                                    <div>
                                        <i class="bi-cloud-upload fs-1 text-muted"></i>
                                        <p class="mb-0 mt-2">Click to upload images</p>
                                        <small class="text-muted">JPG, PNG, GIF, WebP (max 5 images)</small>
                                    </div>
                                </div>
                                <div id="imagePreview" class="image-preview"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-lg px-5">
                            <i class="bi-plus-circle me-2"></i>
                            Add Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function previewImages(input) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            if (input.files) {
                const filesArray = Array.from(input.files);
                const maxFiles = 5;
                
                filesArray.slice(0, maxFiles).forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.alt = `Preview ${index + 1}`;
                            preview.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    }
                });
                
                if (filesArray.length > maxFiles) {
                    const warning = document.createElement('div');
                    warning.className = 'alert alert-warning mt-2';
                    warning.innerHTML = `<i class="bi-exclamation-triangle me-1"></i> Only the first ${maxFiles} images will be uploaded.`;
                    preview.appendChild(warning);
                }
            }
        }
    </script>
</body>
</html> 