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
    <title>Supplier Products - Hanouty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
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
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link text-white" aria-current="page" href="/hanouty/view/front_office/router.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="/hanouty/view/front_office/router.php?action=common-products">Common Products</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="/hanouty/view/front_office/router.php?action=flash-sale">Flash Sale</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="/hanouty/view/front_office/router.php?action=about-us">About Us</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link text-white" href="/hanouty/view/front_office/router.php?action=profile">Profile</a></li>
                    <?php endif; ?>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <!-- Search Form -->
                    <form class="d-flex me-2" method="GET" action="/hanouty/view/front_office/router.php">
                        <div class="input-group" style="min-width: 300px;">
                            <input class="form-control border-end-0" type="search" name="search" placeholder="Search products..." value="<?= isset($searchTerm) ? htmlspecialchars($searchTerm) : '' ?>">
                            <button class="btn btn-outline-light" type="submit" style="border-left: none;">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Cart and User Menu -->
                    <div class="d-flex align-items-center">
                        <a href="router.php?action=cart" class="btn btn-outline-light position-relative me-2">
                            <i class="bi bi-cart"></i>
                            <?php if (isset($_SESSION['cart']) && array_sum($_SESSION['cart']) > 0): ?>
                                <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?= array_sum($_SESSION['cart']) ?>
                                </span>
                            <?php endif; ?>
                        </a>

                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="dropdown">
                                <button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi-person-fill me-1"></i>
                                    <?= htmlspecialchars($_SESSION['user_name']) ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="router.php?action=profile">Profile</a></li>
                                    <?php if ($_SESSION['user_role'] === 'supplier'): ?>
                                        <li><a class="dropdown-item" href="../back_office/index.php">Dashboard</a></li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="router.php?action=logout">Logout</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <button class="btn btn-outline-light" type="button" onclick="showLoginModal()">
                                <i class="bi-person-fill me-1"></i>
                                Login
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <a href="router.php" class="btn btn-outline-dark mb-4">&larr; Back to Home</a>
        <div class="mb-4">
            <h2 class="fw-bold mb-1">Products by <?= htmlspecialchars($supplier['business_name'] ?: $supplier['name']) ?></h2>
            <?php if ($supplier['bio']): ?>
                <div class="text-muted mb-2"> <?= htmlspecialchars($supplier['bio']) ?> </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $idx => $product): ?>
                <div class="featured-spot-product p-4 d-flex flex-column flex-md-row align-items-center gap-4 mb-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 1rem; box-shadow: 0 6px 24px rgba(25,135,84,0.08);">
                    <?php $images = $product['images'] ? json_decode($product['images'], true) : []; ?>
                    <?php if (!empty($images)): ?>
                        <?php $carouselId = 'supplier-carousel-' . $product['id'] . '-' . $idx; ?>
                        <div id="<?= $carouselId ?>" class="carousel slide flex-shrink-0" data-bs-ride="carousel" style="width: 340px; max-width: 100%;">
                            <div class="carousel-inner rounded-3 shadow">
                                <?php foreach ($images as $imgIdx => $img): ?>
                                    <div class="carousel-item<?= $imgIdx === 0 ? ' active' : '' ?>">
                                        <img src="<?= htmlspecialchars($img) ?>" class="d-block w-100" style="height: 260px; object-fit: cover; border-radius: 1rem;" alt="Product Image <?= $imgIdx + 1 ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($images) > 1): ?>
                                <button class="carousel-control-prev" type="button" data-bs-target="#<?= $carouselId ?>" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#<?= $carouselId ?>" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <img src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" class="d-block w-100" style="width:340px; height:260px; object-fit:cover; border-radius:1rem;" alt="No Image">
                    <?php endif; ?>
                    <div class="flex-grow-1">
                        <h3 class="fw-bold mb-2" style="font-size: 2rem; color: #198754; letter-spacing: -1px;">
                            <?= htmlspecialchars($product['title']) ?>
                        </h3>
                        <p class="mb-2" style="font-size: 1.1rem; color: #444; min-height: 32px;"> <?= htmlspecialchars($product['description']) ?> </p>
                        <div class="d-flex align-items-center mb-3">
                            <span class="fs-4 fw-bold text-success me-3"><?= htmlspecialchars(number_format($product['price'], 2)) ?> DT</span>
                        </div>
                        <div class="product-list-btns">
                            <a href="router.php?action=product&id=<?= $product['id'] ?>" class="btn btn-outline-dark btn-lg">View Details</a>
                            <?php if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'supplier'): ?>
                                <form method="POST" action="router.php?action=add-to-cart&id=<?= $product['id'] ?>" style="display:inline-block; margin-left:8px;">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-success btn-lg">Buy</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">This supplier has no products yet.</div>
        <?php endif; ?>
    </div>

    <!-- Footer-->
    <footer class="py-5 bg-dark">
        <div class="container">
            <p class="m-0 text-center text-white">Copyright &copy; Hanouty <?= date('Y') ?></p>
        </div>
    </footer>

    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Modal for Login -->
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Login to Hanouty</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="loginForm" method="POST" action="router.php?action=login">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showLoginModal() {
            var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        }
    </script>
</body>
</html> 