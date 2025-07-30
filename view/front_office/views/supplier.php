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
    <title>Supplier Details - Hanouty</title>
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
    <div class="container py-5">
        <a href="router.php" class="btn btn-outline-dark mb-4">&larr; Back to Home</a>
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <?php if (!empty($supplier['profile_image'])): ?>
                        <img src="<?= htmlspecialchars($supplier['profile_image']) ?>" alt="Profile" class="rounded-circle me-3" width="60" height="60">
                    <?php endif; ?>
                    <div>
                        <h3 class="mb-0"><?= htmlspecialchars($supplier['business_name'] ?: $supplier['name']) ?></h3>
                        <?php if ($supplier['is_verified']): ?>
                            <span class="badge bg-success">Verified</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (!empty($supplier['bio'])): ?>
                    <p class="mb-2 text-muted"><?= htmlspecialchars($supplier['bio']) ?></p>
                <?php endif; ?>
                <small class="text-muted">Total products: <?= count($products) ?></small>
            </div>
        </div>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <img src="<?= $product['images'] ? json_decode($product['images'], true)[0] : 'https://dummyimage.com/450x300/dee2e6/6c757d.jpg' ?>" class="card-img-top" alt="<?= htmlspecialchars($product['title']) ?>">
                        <div class="card-body">
                            <h5 class="card-title mb-1"><?= htmlspecialchars($product['title']) ?></h5>
                            <div class="text-success fw-bold mb-2">$<?= number_format($product['price'], 2) ?></div>
                            <a href="router.php?action=product&id=<?= $product['id'] ?>" class="btn btn-outline-dark">Details</a>
                            <?php if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'supplier'): ?>
                                <form id="buy-form-<?= $product['id'] ?>" method="POST" action="router.php?action=add-to-cart&id=<?= $product['id'] ?>" style="display:inline-block; margin-left:8px;">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-success">Buy</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<!-- Footer-->
<footer class="py-5 bg-dark">
    <div class="container">
        <p class="m-0 text-center text-white">Copyright &copy; Hanouty 2025</p>
    </div>
</footer>
<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="loginModalLabel">Login Required</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>You must be logged in to add products to your cart.</p>
        <a href="router.php?action=login" class="btn btn-success">Go to Login</a>
      </div>
    </div>
  </div>
</div>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showLoginModal() {
    var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    loginModal.show();
}
<?php foreach ($products as $product): ?>
document.getElementById('buy-form-<?= $product['id'] ?>').addEventListener('submit', function(e) {
    <?php if (!isset($_SESSION['user_id'])): ?>
        e.preventDefault();
        showLoginModal();
        return false;
    <?php else: ?>
        e.preventDefault();
        var form = this;
        var formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.ok ? response.text() : Promise.reject())
        .then(() => {
            fetch('router.php?action=cart-count')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('cart-count').textContent = data.count;
                });
            var toast = new bootstrap.Toast(document.getElementById('cartToast'));
            toast.show();
        });
    <?php endif; ?>
});
<?php endforeach; ?>
</script>
</body>
</html> 