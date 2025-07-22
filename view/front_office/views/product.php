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
    <title>Product Details - Hanouty</title>
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
        <a href="javascript:history.back()" class="btn btn-outline-dark mb-4">&larr; Back</a>
        <div class="card mb-4">
            <div class="row g-0">
                <div class="col-md-5">
                    <?php 
                    $images = $product['images'] ? json_decode($product['images'], true) : [];
                    $mainImage = $images[0] ?? 'https://dummyimage.com/450x300/dee2e6/6c757d.jpg';
                    ?>
                    <div id="main-image-container">
                        <img id="main-product-image" src="<?= htmlspecialchars($mainImage) ?>" class="img-fluid rounded-start w-100" alt="<?= htmlspecialchars($product['title']) ?>">
                    </div>
                    <?php if (count($images) > 1): ?>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <?php foreach ($images as $idx => $img): ?>
                                <img src="<?= htmlspecialchars($img) ?>" class="rounded product-thumb<?= $idx === 0 ? ' selected-thumb' : '' ?>" style="width: 60px; height: 60px; object-fit: cover; cursor:pointer; border:2px solid #eee;" alt="" data-img="<?= htmlspecialchars($img) ?>">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-7">
                    <div class="card-body">
                        <h2 class="card-title mb-2"><?= htmlspecialchars($product['title']) ?></h2>
                        <div class="mb-3 text-success fw-bold fs-4">$<?= number_format($product['price'], 2) ?></div>
                        <?php if (!empty($product['category'])): ?>
                            <span class="badge bg-secondary mb-3">Category: <?= htmlspecialchars($product['category']) ?></span><br>
                        <?php endif; ?>
                        <p class="card-text mb-4"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                        <hr>
                        <div class="d-flex align-items-center">
                            <?php if (!empty($product['profile_image'])): ?>
                                <img src="<?= htmlspecialchars($product['profile_image']) ?>" alt="Supplier" class="rounded-circle me-2" width="40" height="40">
                            <?php endif; ?>
                            <div>
                                <?php
                                $supplierDisplayName = !empty($product['business_name']) ? $product['business_name'] : (!empty($product['supplier_name']) ? $product['supplier_name'] : 'Unknown Supplier');
                                ?>
                                <div class="fw-bold">Supplier: <?= htmlspecialchars($supplierDisplayName) ?></div>
                                <?php if (!empty($product['bio'])): ?>
                                    <small class="text-muted"><?= htmlspecialchars($product['bio']) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <form id="buy-form" method="POST" action="router.php?action=add-to-cart&id=<?= $product['id'] ?>" class="mt-4">
                        <div class="input-group mb-3" style="max-width: 200px;">
                            <input type="number" name="quantity" class="form-control" value="1" min="1" style="min-width: 60px;" required>
                            <button type="submit" class="btn btn-success">Buy</button>
                        </div>
                    </form>
                </div>
            </div>
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
// Image thumbnail click to change main image
const mainImg = document.getElementById('main-product-image');
document.querySelectorAll('.product-thumb').forEach(function(thumb) {
    thumb.addEventListener('click', function() {
        mainImg.src = this.getAttribute('data-img');
        document.querySelectorAll('.product-thumb').forEach(t => t.classList.remove('selected-thumb'));
        this.classList.add('selected-thumb');
    });
});

function showLoginModal() {
    var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    loginModal.show();
}

document.getElementById('buy-form').addEventListener('submit', function(e) {
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
            // Update cart count
            fetch('router.php?action=cart-count')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('cart-count').textContent = data.count;
                });
            // Show toast
            var toast = new bootstrap.Toast(document.getElementById('cartToast'));
            toast.show();
        });
    <?php endif; ?>
});
</script>
<style>
.selected-thumb {
    border: 2px solid #198754 !important;
    box-shadow: 0 0 0 2px #19875433;
}
#main-image-container {
    width: 100%;
    height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fafafa;
    border-radius: 0.5rem;
    margin-bottom: 10px;
}
#main-product-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    display: block;
}
</style>
</body>
</html> 