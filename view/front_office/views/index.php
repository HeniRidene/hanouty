<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="Hanouty - Discover amazing products from verified suppliers" />
        <meta name="author" content="Hanouty" />
        <title>Hanouty - Discover Amazing Products</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
        <style>
            .supplier-widget {
                border: 1px solid #dee2e6;
                border-radius: 0.75rem;
                margin-bottom: 2.5rem;
                background: white;
                box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                transition: box-shadow 0.3s ease;
            }
            .supplier-widget:hover {
                box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            }
            .supplier-header {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                padding: 1.5rem;
                border-bottom: 1px solid #dee2e6;
                border-radius: 0.75rem 0.75rem 0 0;
            }
            .supplier-name {
                font-weight: 700;
                color: #212529;
                margin: 0;
                font-size: 1.5rem;
            }
            .supplier-bio {
                color: #6c757d;
                font-size: 0.95rem;
                margin: 0.5rem 0 0 0;
                line-height: 1.4;
            }
            .products-grid {
                padding: 1.5rem;
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 1.25rem;
                min-height: 200px;
            }
            .product-card {
                border: 1px solid #e9ecef;
                border-radius: 0.5rem;
                overflow: hidden;
                transition: all 0.3s ease;
                background: white;
            }
            .product-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 8px 20px rgba(0,0,0,0.15);
                border-color: #198754;
            }
            .product-image {
                width: 100%;
                height: 160px;
                object-fit: cover;
                transition: transform 0.3s ease;
            }
            .product-card:hover .product-image {
                transform: scale(1.05);
            }
            .product-info {
                padding: 1rem;
            }
            .product-title {
                font-weight: 600;
                margin-bottom: 0.75rem;
                font-size: 0.95rem;
                color: #212529;
                line-height: 1.3;
            }
            .product-price {
                color: #198754;
                font-weight: bold;
                font-size: 1.2rem;
            }
            .verified-badge {
                background: linear-gradient(135deg, #198754 0%, #20c997 100%);
                color: white;
                padding: 0.35rem 0.75rem;
                border-radius: 0.375rem;
                font-size: 0.8rem;
                margin-left: 0.75rem;
                font-weight: 600;
                box-shadow: 0 2px 4px rgba(25, 135, 84, 0.3);
            }
            .pagination .page-link {
                color: #198754;
                border-color: #dee2e6;
            }
            .pagination .page-item.active .page-link {
                background-color: #198754;
                border-color: #198754;
            }
            .pagination .page-link:hover {
                color: #146c43;
                background-color: #e9ecef;
                border-color: #dee2e6;
            }
            .login-modal {
                display: none;
            }
            .login-modal.show {
                display: block;
            }
            .modal-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 1040;
            }
            .modal-content {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: white;
                padding: 2rem;
                border-radius: 0.5rem;
                z-index: 1050;
                max-width: 400px;
                width: 90%;
            }
            .close-btn {
                position: absolute;
                top: 1rem;
                right: 1rem;
                background: none;
                border: none;
                font-size: 1.5rem;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container px-4 px-lg-5">
                <a class="navbar-brand" href="router.php">
                    <strong>Hanouty</strong>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="router.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#suppliers">Suppliers</a>
                        </li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="profile.php">Profile</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                    
                    <!-- Search Form -->
                    <form class="d-flex me-3" method="GET" action="router.php">
                        <input class="form-control me-2" type="search" name="search" placeholder="Search products..." value="<?= htmlspecialchars($searchTerm) ?>">
                        <button class="btn btn-outline-dark" type="submit">Search</button>
                    </form>
                    
                    <!-- User Menu -->
                    <div class="d-flex">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($_SESSION['user_role'] === 'supplier'): ?>
                                <a href="router.php?action=add-product" class="btn btn-success me-2">
                                    <i class="bi-plus-circle me-1"></i>
                                    Add Product
                                </a>
                            <?php endif; ?>
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
                                    <li><a class="dropdown-item" href="router.php?action=logout">Logout</a></li>
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

        <!-- Header-->
        <header class="bg-dark py-5">
            <div class="container px-4 px-lg-5 my-5">
                <div class="text-center text-white">
                    <h1 class="display-4 fw-bolder">Discover Amazing Products</h1>
                    <p class="lead fw-normal text-white-50 mb-0">Connect with verified suppliers and find the best products</p>
                </div>
            </div>
        </header>

        <!-- Search Results Section -->
        <?php if ($searchTerm && !empty($searchResults)): ?>
        <section class="py-5">
            <div class="container px-4 px-lg-5">
                <h2 class="mb-4">Search Results for "<?= htmlspecialchars($searchTerm) ?>"</h2>
                <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                    <?php foreach ($searchResults as $product): ?>
                    <div class="col mb-5">
                        <div class="card h-100">
                            <img class="card-img-top" src="<?= $product['images'] ? json_decode($product['images'], true)[0] : 'https://dummyimage.com/450x300/dee2e6/6c757d.jpg' ?>" alt="<?= htmlspecialchars($product['title']) ?>" />
                            <div class="card-body p-4">
                                <div class="text-center">
                                    <h5 class="fw-bolder"><?= htmlspecialchars($product['title']) ?></h5>
                                    <p class="text-muted small">by <?= htmlspecialchars($product['business_name'] ?: $product['supplier_name']) ?></p>
                                    <div class="fw-bold text-success">$<?= number_format($product['price'], 2) ?></div>
                                </div>
                            </div>
                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                <div class="text-center">
                                    <a class="btn btn-outline-dark mt-auto" href="router.php?action=product&id=<?= $product['id'] ?>">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Suppliers Section -->
        <section class="py-5" id="suppliers">
            <div class="container px-4 px-lg-5">
                <h2 class="text-center mb-5">Our Verified Suppliers</h2>
                
                <?php if (empty($suppliers)): ?>
                    <div class="text-center">
                        <p class="text-muted">No suppliers available at the moment.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($suppliers as $supplier): ?>
                        <div class="supplier-widget">
                            <!-- Supplier Name Header -->
                            <div class="supplier-header">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h4 class="supplier-name mb-0">
                                            <?= htmlspecialchars($supplier['business_name'] ?: $supplier['name']) ?>
                                            <?php if ($supplier['is_verified']): ?>
                                                <span class="verified-badge">✓ Verified</span>
                                            <?php endif; ?>
                                        </h4>
                                        <?php if ($supplier['bio']): ?>
                                            <p class="supplier-bio mb-0 mt-1"><?= htmlspecialchars($supplier['bio']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block"><?= $supplier['products_count'] ?> products</small>
                                        <?php if ($supplier['profile_image']): ?>
                                            <img src="<?= htmlspecialchars($supplier['profile_image']) ?>" alt="Profile" class="rounded-circle" width="40" height="40">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Products List (vertical) -->
                            <div class="products-list">
                                <?php 
                                $supplierProducts = $controller->getSupplierProducts($supplier['id']);
                                $displayProducts = array_slice($supplierProducts, 0, 6); // Show max 6 products
                                ?>
                                <?php foreach ($displayProducts as $product): ?>
                                <div class="product-card mb-3 d-flex align-items-center" style="flex-direction: row;">
                                    <img class="product-image me-3" style="width: 150px; height: 100px; object-fit: cover;" src="<?= $product['images'] ? json_decode($product['images'], true)[0] : 'https://dummyimage.com/450x300/dee2e6/6c757d.jpg' ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                                    <div class="product-info flex-grow-1">
                                        <h6 class="product-title mb-1"><?= htmlspecialchars($product['title']) ?></h6>
                                        <div class="product-price mb-2">$<?= number_format($product['price'], 2) ?></div>
                                    </div>
                                    <div>
                                        <a href="router.php?action=product&id=<?= $product['id'] ?>" class="btn btn-outline-dark">Details</a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <!-- Pagination -->
                    <?php if ($pagination['total_pages'] > 1): ?>
                    <nav aria-label="Suppliers pagination" class="mt-5">
                        <ul class="pagination justify-content-center">
                            <?php if ($pagination['current_page'] > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="router.php?page=<?= $pagination['current_page'] - 1 ?><?= $searchTerm ? '&search=' . urlencode($searchTerm) : '' ?>">Previous</a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                                <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                                    <a class="page-link" href="router.php?page=<?= $i ?><?= $searchTerm ? '&search=' . urlencode($searchTerm) : '' ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="router.php?page=<?= $pagination['current_page'] + 1 ?><?= $searchTerm ? '&search=' . urlencode($searchTerm) : '' ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Footer-->
        <footer class="py-5 bg-dark">
            <div class="container">
                <p class="m-0 text-center text-white">Copyright &copy; Hanouty 2025</p>
            </div>
        </footer>

        <!-- Login Modal -->
        <div id="loginModal" class="login-modal">
            <div class="modal-backdrop" onclick="hideLoginModal()"></div>
            <div class="modal-content">
                <button type="button" class="close-btn" onclick="hideLoginModal()">&times;</button>
                <h4 class="mb-4">Login to Hanouty</h4>
                
                <?php if (isset($loginError)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($loginError) ?></div>
                <?php endif; ?>
                
                <form method="POST" action="router.php?action=login">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" name="login" class="btn btn-dark">Login</button>
                    </div>
                </form>
                
                <div class="text-center mt-3">
                    <p class="mb-0">Don't have an account? <a href="router.php?action=register">Register here</a></p>
                </div>
            </div>
        </div>

        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
        
        <script>
            function showLoginModal() {
                document.getElementById('loginModal').classList.add('show');
            }
            
            function hideLoginModal() {
                document.getElementById('loginModal').classList.remove('show');
            }
            
            // Close modal on escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    hideLoginModal();
                }
            });
        </script>
    </body>
</html> 