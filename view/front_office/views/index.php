<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
?>
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
            .featured-suppliers {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }
            .supplier-widget {
                width: 100%;
            }
            .badge-premium {
                background-color: #198754;
                color: white;
                padding: 0.25rem 0.5rem;
                border-radius: 0.25rem;
                font-size: 0.8rem;
                font-weight: 600;
            }
            .badge-secondary {
                background-color: #e9ecef;
                color: #6c757d;
                padding: 0.25rem 0.5rem;
                border-radius: 0.25rem;
                font-size: 0.8rem;
                font-weight: 600;
            }
            /* Custom Featured Pagination Styles */
            .custom-featured-pagination .page-item {
                margin: 0 0.25rem;
            }
            .custom-featured-pagination .page-link {
                border-radius: 1.5rem;
                box-shadow: 0 2px 8px rgba(25, 135, 84, 0.08);
                color: #198754;
                font-weight: 600;
                background: #fff;
                border: 1px solid #dee2e6;
                transition: all 0.2s;
                padding: 0.5rem 1.25rem;
            }
            .custom-featured-pagination .page-item.active .page-link,
            .custom-featured-pagination .page-link:hover {
                background: linear-gradient(135deg, #198754 0%, #20c997 100%);
                color: #fff;
                border-color: #198754;
                box-shadow: 0 4px 16px rgba(25, 135, 84, 0.15);
            }
            .custom-featured-pagination .page-link:focus {
                outline: none;
                box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
            }
            .featured-spot-product {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                border-radius: 1rem;
                box-shadow: 0 6px 24px rgba(25,135,84,0.08);
                margin-bottom: 1.5rem;
            }
            .featured-spot-product h3 {
                color: #198754;
            }
            .featured-spot-product .carousel-inner {
                border-radius: 1rem;
            }
            .featured-spot-product .btn-warning {
                background: linear-gradient(135deg, #ffc107 0%, #ffecb3 100%);
                color: #212529;
                border: none;
                font-weight: 600;
                box-shadow: 0 2px 8px rgba(255,193,7,0.08);
                transition: background 0.2s;
            }
            .featured-spot-product .btn-warning:hover {
                background: linear-gradient(135deg, #ffb300 0%, #ffe082 100%);
                color: #212529;
            }
            .navbar .nav-link:hover { color: #fff !important; text-decoration: underline; }
            .navbar .btn-outline-dark { border-color: #fff; color: #fff; }
            .navbar .btn-outline-dark:hover { background: #fff; color: #198754; }
        </style>
    </head>
    <body>
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
                        <li class="nav-item"><a class="nav-link text-white" href="/hanouty/view/front_office/router.php?action=about-us">About Us</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="nav-item"><a class="nav-link text-white" href="/hanouty/view/front_office/router.php?action=profile">Profile</a></li>
                        <?php endif; ?>
                    </ul>
                    
                    <!-- Search Form -->
                    <form class="d-flex me-3" method="GET" action="/hanouty/view/front_office/router.php">
                        <input class="form-control me-2" type="search" name="search" placeholder="Search products..." value="<?= htmlspecialchars($searchTerm) ?>">
                        <button class="btn btn-outline-dark" type="submit">Search</button>
                    </form>
                    
                    <!-- User Menu -->
                    <div class="d-flex align-items-center">
                        <a href="router.php?action=cart" class="btn btn-outline-dark position-relative me-2">
                            <i class="bi bi-cart"></i>
                            <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0 ?>
                            </span>
                        </a>
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

        <!-- Header-->
        <header class="bg-dark py-5" style="margin-bottom: 3rem;">
            <div class="container px-4 px-lg-5 my-5">
                <div class="text-center text-white">
                    <h1 class="display-4 fw-bolder">Discover Amazing Products</h1>
                    <p class="lead fw-normal text-white-50 mb-0">Connect with verified suppliers and find the best products</p>
                </div>
            </div>
        </header>

        <!-- Add extra spacing before Featured Spots section -->
        <div style="height: 40px;"></div>

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
                                    <form id="buy-form-search-<?= $product['id'] ?>" method="POST" action="router.php?action=add-to-cart&id=<?= $product['id'] ?>" style="display:inline-block; margin-left:8px;">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-success">Buy</button>
                                    </form>
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
        <?php /*
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'supplier') {
            // Supplier grouping section removed as per user request
        }
        */ ?>

        <!-- Main Product Display: Spots for all, controls only for suppliers -->
        <section class="py-5">
            <div class="container px-4 px-lg-5">
                <h2 class="text-center mb-5">Featured Spots</h2>
                <div class="featured-suppliers">
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <div class="supplier-widget spot-<?= $i ?>">
                            <?php if (isset($spots[$i]) && $spots[$i]['product_id']): ?>
                                <?php 
                                $productImages = [];
                                if (!empty($spots[$i]['images'])) {
                                    $productImages = json_decode($spots[$i]['images'], true);
                                }
                                ?>
                                <div class="featured-spot-product p-4 d-flex flex-column flex-md-row align-items-center gap-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 1rem; box-shadow: 0 6px 24px rgba(25,135,84,0.08);">
                                    <?php if (!empty($productImages)): ?>
                                        <div id="spot-carousel-<?= $i ?>" class="carousel slide flex-shrink-0" data-bs-ride="carousel" style="width: 340px; max-width: 100%;">
                                            <div class="carousel-inner rounded-3 shadow">
                                                <?php foreach ($productImages as $imgIdx => $img): ?>
                                                    <div class="carousel-item<?= $imgIdx === 0 ? ' active' : '' ?>">
                                                        <img src="<?= htmlspecialchars($img) ?>" class="d-block w-100" style="height: 260px; object-fit: cover; border-radius: 1rem;" alt="Product Image <?= $imgIdx + 1 ?>">
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <?php if (count($productImages) > 1): ?>
                                                <button class="carousel-control-prev" type="button" data-bs-target="#spot-carousel-<?= $i ?>" data-bs-slide="prev">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                    <span class="visually-hidden">Previous</span>
                                                </button>
                                                <button class="carousel-control-next" type="button" data-bs-target="#spot-carousel-<?= $i ?>" data-bs-slide="next">
                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                    <span class="visually-hidden">Next</span>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-grow-1">
                                        <h3 class="fw-bold mb-2" style="font-size: 2rem; color: #198754; letter-spacing: -1px;">
                                            <?= htmlspecialchars($spots[$i]['title']) ?> 
                                        </h3>
                                        <p class="mb-2" style="font-size: 1.1rem; color: #444;"><?= htmlspecialchars($spots[$i]['description']) ?></p>
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="fs-4 fw-bold text-success me-3"><?= htmlspecialchars(number_format($spots[$i]['price'], 2)) ?> DT</span>
                                        </div>
                                        <a href="router.php?action=product&id=<?= $spots[$i]['product_id'] ?>" class="btn btn-outline-dark">View Details</a>
                                        <form id="buy-form-spot-<?= $spots[$i]['product_id'] ?>" method="POST" action="router.php?action=add-to-cart&id=<?= $spots[$i]['product_id'] ?>" style="display:inline-block; margin-left:8px;">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-success">Buy</button>
                                        </form>
                                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'supplier' && $supplierId == $spots[$i]['supplier_id']): ?>
                                            <a href="router.php?action=edit-product&id=<?= $spots[$i]['product_id'] ?>&featured_page=<?= $featuredPage ?>&spot=<?= $i ?>" class="btn btn-warning px-3 py-2 fw-bold" title="Modify Offer">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="router.php?action=delete-product&id=<?= $spots[$i]['product_id'] ?>&featured_page=<?= $featuredPage ?>&spot=<?= $i ?>" class="btn btn-danger px-3 py-2 ms-2" title="Delete Product" onclick="return confirm('Are you sure you want to delete this product?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- If spot is empty, show nothing for clients/guests, or controls for suppliers -->
                                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'supplier'): ?>
                                    <h3>Spot n°<?= $i ?></h3>
                                    <?php if (isset($spots[$i]['supplier_id']) && $spots[$i]['supplier_id'] == $supplierId && empty($spots[$i]['product_id'])): ?>
                                        <button class="btn" disabled>Spot Owned</button>
                                        <a href="router.php?action=add-product&featured_page=<?= $featuredPage ?>&spot=<?= $i ?>" class="btn btn-success ms-2">Add Product</a>
                                    <?php elseif (isset($spots[$i]['supplier_id']) && $spots[$i]['supplier_id'] != $supplierId): ?>
                                        <button class="btn btn-secondary" disabled>Can't purchase this spot</button>
                                    <?php else: ?>
                                        <a href="#" class="btn buy-spot-btn" data-spot="<?= $i ?>" data-page="<?= $featuredPage ?>">
                                            Buy this spot for <?= $spotPrices[$i] ?> DT
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                </div>
                <!-- Pagination -->
                <nav aria-label="Featured spots pagination" class="mt-4">
                    <ul class="pagination justify-content-center custom-featured-pagination">
                        <?php for (
                            $p = 1; $p <= $totalPages; $p++): ?>
                            <li class="page-item<?= $p == $featuredPage ? ' active' : '' ?>">
                                <?php if ($p == $featuredPage): ?>
                                    <span class="page-link">Page <?= $p ?></span>
                                <?php else: ?>
                                    <a class="page-link" href="router.php?featured_page=<?= $p ?>">Page <?= $p ?></a>
                                <?php endif; ?>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        </section>

        <!-- Footer-->
        <footer class="py-5 bg-dark">
            <div class="container">
                <p class="m-0 text-center text-white">Copyright &copy; Hanouty 2025</p>
            </div>
        </footer>

        <!-- Toast for add to cart -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="cartToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        Product added to cart!
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>

        <!-- Login Modal -->
        <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Login to Hanouty</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form id="loginForm" method="POST" action="router.php?action=login">
                <div class="modal-body">
                  <div id="loginError" class="alert alert-danger d-none"></div>
                  <div class="mb-3">
                    <label for="loginEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="loginEmail" name="email" required>
                  </div>
                  <div class="mb-3">
                    <label for="loginPassword" class="form-label">Password</label>
                    <input type="password" class="form-control" id="loginPassword" name="password" required>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-success w-100">Login</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
        
        <script>
            // Remove login modal and showLoginModal() JS
        </script>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.buy-spot-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var spot = this.getAttribute('data-spot');
                    var page = this.getAttribute('data-page');
                    var button = this;
                    fetch('buy_spot.php?featured_page=' + page + '&spot=' + spot)
                        .then(response => response.text())
                        .then(data => {
                            if (data === 'success') {
                                // Create a wrapper div for the new buttons
                                var wrapper = document.createElement('div');
                                wrapper.style.display = 'inline-block';
                                // Spot Owned button
                                var ownedBtn = document.createElement('button');
                                ownedBtn.className = 'btn';
                                ownedBtn.disabled = true;
                                ownedBtn.textContent = 'Spot Owned';
                                // Add Product button
                                var addBtn = document.createElement('a');
                                addBtn.href = 'router.php?action=add-product&featured_page=' + page + '&spot=' + spot;
                                addBtn.className = 'btn btn-success ms-2';
                                addBtn.textContent = 'Add Product';
                                // Add both to wrapper
                                wrapper.appendChild(ownedBtn);
                                wrapper.appendChild(addBtn);
                                // Replace the original button with the wrapper
                                button.parentNode.replaceChild(wrapper, button);
                            } else if (data === 'spot_taken') {
                                button.outerHTML = '<button class="btn" disabled>Spot Owned</button>';
                            }
                        });
                });
            });
        });
        </script>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
          window.showLoginModal = function() {
            var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
          }
          const loginForm = document.getElementById('loginForm');
          if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
              e.preventDefault();
              document.getElementById('loginError').classList.add('d-none');
              const formData = new FormData(this);
              fetch('router.php?action=login', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
              })
              .then(res => res.json())
              .then(data => {
                if (data.success) {
                  // Hide modal and reload after a short delay
                  var modalEl = document.getElementById('loginModal');
                  var modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                  modalInstance.hide();
                  setTimeout(function() {
                    window.location.reload();
                  }, 500);
                } else {
                  document.getElementById('loginError').textContent = data.error || 'Login failed.';
                  document.getElementById('loginError').classList.remove('d-none');
                }
              })
              .catch((err) => {
                document.getElementById('loginError').textContent = 'Network error. Please try again.';
                document.getElementById('loginError').classList.remove('d-none');
                console.error('Login AJAX error:', err);
              });
            });
          }
        });
        </script>
        
    </body>
</html> 