<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Edit product - Hanouty" />
    <meta name="author" content="Hanouty" />
    <title>Edit Product - Hanouty</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
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
            background: linear-gradient(135deg, #ffc107 0%, #ffe082 100%);
            color: #212529;
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
            border-color: #ffc107;
            background: #fffbe6;
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
<!-- Navigation-->
<nav class="navbar navbar-expand-lg navbar-light" style="background: linear-gradient(135deg, #198754 0%, #20c997 100%); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
    <div class="container px-4 px-lg-5">
        <a class="navbar-brand text-white" href="/hanouty/view/front_office/router.php"><strong>Hanouty</strong></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="#navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
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
    <div class="container mt-3">
        <a href="router.php?featured_page=<?= htmlspecialchars($featuredPage) ?>" class="btn btn-outline-dark">
            <i class="bi-arrow-left me-1"></i>
            Back to Featured Spots
        </a>
    </div>
    <div class="container py-5">
        <div class="form-container">
            <div class="form-header">
                <h1 class="mb-0">
                    <i class="bi-pencil-square me-2"></i>
                    Edit Product Offer
                </h1>
                <p class="mb-0 mt-2">Modify your featured spot offer</p>
            </div>
            <div class="form-body">
                <?php if (isset($editProductError)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi-exclamation-triangle me-2"></i>
                        <?= htmlspecialchars($editProductError) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <form method="POST" action="router.php?action=edit-product&id=<?= $product['id'] ?>&featured_page=<?= htmlspecialchars($featuredPage) ?>&spot=<?= htmlspecialchars($spot) ?>" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">
                                    <i class="bi-tag me-1"></i>
                                    Product Title *
                                </label>
                                <input type="text" class="form-control" id="title" name="title" required 
                                       placeholder="Enter product title" value="<?= htmlspecialchars($_POST['title'] ?? $product['title']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    <i class="bi-text-paragraph me-1"></i>
                                    Description *
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="4" required 
                                          placeholder="Describe your product in detail"><?= htmlspecialchars($_POST['description'] ?? $product['description']) ?></textarea>
                            </div>
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
                                                   placeholder="0.00" value="<?= htmlspecialchars($_POST['price'] ?? $product['price']) ?>">
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
                                            <option value="Electronics" <?= (($_POST['category'] ?? $product['category']) === 'Electronics') ? 'selected' : '' ?>>Electronics</option>
                                            <option value="Clothing" <?= (($_POST['category'] ?? $product['category']) === 'Clothing') ? 'selected' : '' ?>>Clothing</option>
                                            <option value="Home & Garden" <?= (($_POST['category'] ?? $product['category']) === 'Home & Garden') ? 'selected' : '' ?>>Home & Garden</option>
                                            <option value="Sports" <?= (($_POST['category'] ?? $product['category']) === 'Sports') ? 'selected' : '' ?>>Sports</option>
                                            <option value="Books" <?= (($_POST['category'] ?? $product['category']) === 'Books') ? 'selected' : '' ?>>Books</option>
                                            <option value="Toys" <?= (($_POST['category'] ?? $product['category']) === 'Toys') ? 'selected' : '' ?>>Toys</option>
                                            <option value="Automotive" <?= (($_POST['category'] ?? $product['category']) === 'Automotive') ? 'selected' : '' ?>>Automotive</option>
                                            <option value="Health & Beauty" <?= (($_POST['category'] ?? $product['category']) === 'Health & Beauty') ? 'selected' : '' ?>>Health & Beauty</option>
                                            <option value="Other" <?= (($_POST['category'] ?? $product['category']) === 'Other') ? 'selected' : '' ?>>Other</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="is_flash_sale" name="is_flash_sale" value="1" <?= (isset($_POST['is_flash_sale']) || ($product['is_flash_sale'] ?? 0) == 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_flash_sale">Add to Flash Sale</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi-images me-1"></i>
                                    Product Images
                                </label>
                                <div class="file-input-wrapper">
                                    <input type="file" id="images" name="images[]" multiple accept="image/*" onchange="previewImages(this)">
                                    <div>
                                        <i class="bi-cloud-upload fs-1 text-muted"></i>
                                        <p class="mb-0 mt-2">Click to upload new images (optional)</p>
                                        <small class="text-muted">JPG, PNG, GIF, WebP (max 5 images)</small>
                                    </div>
                                </div>
                                <div id="imagePreview" class="image-preview">
                                    <?php if (!empty($product['images'])):
                                        $imgs = json_decode($product['images'], true);
                                        foreach ($imgs as $img): ?>
                                            <img src="<?= htmlspecialchars($img) ?>" alt="Current Image">
                                        <?php endforeach;
                                    endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-warning btn-lg px-5">
                            <i class="bi-pencil-square me-2"></i>
                            Save Changes
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
    <script>
    function previewImages(input) {
        var preview = document.getElementById('imagePreview');
        preview.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(function(file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    preview.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
        }
    }
    </script>
</body>
</html> 