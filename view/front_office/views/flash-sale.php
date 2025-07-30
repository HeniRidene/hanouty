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
    <title>Flash Sale - Hanouty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        .flash-header { background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); color: white; padding: 3rem 0; text-align: center; }
        .flash-header h1 { font-size: 3rem; font-weight: bold; }
        .product-card { transition: transform 0.3s; height: 480px; display: flex; flex-direction: column; }
        .product-card:hover { transform: scale(1.05); }
        .card-img-top { height: 250px; object-fit: cover; width: 100%; }
        .card-body { flex: 1 1 auto; display: flex; flex-direction: column; }
        .flash-price { color: #ff4b2b; font-weight: bold; }
        html, body {
            height: 100%;
        }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .container.py-5 {
            flex: 1 0 auto;
        }
        footer {
            flex-shrink: 0;
        }
        .flash-countdown {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 0.5rem;
            letter-spacing: 1px;
        }
        /* Custom switch style for filter */
        .custom-switch .form-check-input {
            width: 2.5em;
            height: 1.5em;
            cursor: pointer;
            background-color: #e9ecef;
            border: 1px solid #adb5bd;
            transition: background-color 0.2s, border-color 0.2s;
        }
        .custom-switch .form-check-input:checked {
            background-color: #ff4b2b;
            border-color: #ff4b2b;
        }
        .custom-switch .form-check-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(255,75,43,0.15);
        }
        .custom-switch .form-check-label {
            font-size: 1.1em;
            font-weight: 600;
            color: #222;
            margin-left: 0.5em;
            display: flex;
            align-items: center;
        }
        .custom-switch .form-check-label .text-success {
            color: #198754 !important;
            font-weight: bold;
        }
        .custom-switch .form-check-label .bi {
            font-size: 1.2em;
            margin-right: 0.3em;
        }
                .card-body form {
            display: inline-block;
            margin-left: 8px;
        }

        .card-body .btn {
            min-width: 120px;
            padding: 0.375rem 0.75rem;
        }
    </style>
</head>
<body style="min-height: 100vh; display: flex; flex-direction: column;">
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
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'supplier'): ?>
            <div class="mb-4 text-end">
                <a href="router.php?action=add-flash-sale-product" class="btn btn-warning btn-lg fw-bold shadow">
                    + Add Flash Sale Product
                </a>
            </div>
        <?php endif; ?>
        
        <!-- Filter Section -->
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-start">
                <div class="form-check form-switch custom-switch">
                    <input class="form-check-input" type="checkbox" id="showOnlyActive">
                    <label class="form-check-label" for="showOnlyActive">
                        Show only active sales
                    </label>
                </div>
            </div>
        </div>
        
        <!-- Flash sale content here -->
        <?php if (!empty($flashProducts)): ?>
            <div class="row g-4" id="flashProductsContainer">
                <?php foreach ($flashProducts as $product): ?>
                    <div class="col-12 col-md-6 col-lg-4 product-item">
                        <div class="card product-card h-100 shadow-sm">
                            <?php
                            $imagePath = 'https://dummyimage.com/450x300/dee2e6/6c757d.jpg';
                            if (!empty($product['images'])) {
                                $imagesArr = @json_decode($product['images'], true);
                                if (is_array($imagesArr) && !empty($imagesArr[0])) {
                                    $firstImg = $imagesArr[0];
                                    if (filter_var($firstImg, FILTER_VALIDATE_URL)) {
                                        $imagePath = $firstImg;
                                    } elseif (strpos($firstImg, '/') === 0) {
                                        $imagePath = $firstImg; // already absolute
                                    } else {
                                        $imagePath = '/hanouty/' . ltrim($firstImg, '/');
                                    }
                                }
                            }
                            ?>
                            <img src="<?= htmlspecialchars($imagePath) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['title']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($product['title']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($product['description']) ?></p>
                                <div class="flash-price mb-2"><?= number_format($product['price'], 2) ?> DT</div>
                                <!-- Countdown Timer -->
                                <?php
                                // Use 'end_time' if available, otherwise set a placeholder (e.g., 24h from created_at)
                                $endTimestamp = null;
                                if (!empty($product['end_time'])) {
                                    $endTimestamp = is_numeric($product['end_time']) ? $product['end_time'] : strtotime($product['end_time']);
                                } elseif (!empty($product['created_at'])) {
                                    $endTimestamp = strtotime($product['created_at']) + 3*3600; // 24h after creation
                                }
                                ?>
                                <?php if ($endTimestamp): ?>
                                    <div class="flash-countdown mb-2" data-end="<?= $endTimestamp ?>"></div>
                                <?php endif; ?>
                                <a href="router.php?action=product&id=<?= $product['id'] ?>" class="btn btn-outline-dark">View Details</a>
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
        <?php else: ?>
            <div class="alert alert-info text-center">No flash sale products available at the moment.</div>
        <?php endif; ?>
    </div>
<!-- Footer-->
<footer class="py-5 bg-dark">
    <div class="container">
        <p class="m-0 text-center text-white">Copyright &copy; Hanouty 2025</p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Countdown for each flash sale product
function updateCountdowns() {
    document.querySelectorAll('.flash-countdown').forEach(function(el) {
        var end = parseInt(el.getAttribute('data-end')) * 1000;
        var now = Date.now();
        var diff = Math.max(0, end - now);
        if (diff <= 0) {
            el.textContent = 'Flash sale ended';
            el.style.color = '#888';
            el.closest('.product-item').setAttribute('data-status', 'ended');
        } else {
            var h = Math.floor(diff / 3600000);
            var m = Math.floor((diff % 3600000) / 60000);
            var s = Math.floor((diff % 60000) / 1000);
            el.textContent = 'Ends in ' + h + 'h ' + m + 'm ' + s + 's';
            el.style.color = '#ff4b2b';
            el.closest('.product-item').setAttribute('data-status', 'active');
        }
    });
}

// Filter products based on checkbox selection
function filterProducts() {
    const showOnlyActive = document.getElementById('showOnlyActive').checked;
    const products = document.querySelectorAll('.product-item');
    
    products.forEach(function(product) {
        const status = product.getAttribute('data-status');
        let shouldShow = true;
        
        if (showOnlyActive && status === 'ended') {
            shouldShow = false;
        }
        
        if (shouldShow) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
    
    // Show message if no products match the filter
    const visibleProducts = document.querySelectorAll('.product-item[style="display: block;"]');
    const noProductsMessage = document.getElementById('noProductsMessage');
    
    if (visibleProducts.length === 0) {
        if (!noProductsMessage) {
            const message = document.createElement('div');
            message.id = 'noProductsMessage';
            message.className = 'col-12 alert alert-info text-center';
            message.textContent = 'No active flash sales available.';
            document.getElementById('flashProductsContainer').appendChild(message);
        }
    } else {
        if (noProductsMessage) {
            noProductsMessage.remove();
        }
    }
}

// Event listeners for checkboxes
document.addEventListener('DOMContentLoaded', function() {
    updateCountdowns();
    
    // Add event listener to checkbox
    document.getElementById('showOnlyActive').addEventListener('change', filterProducts);
    
    // Initial filter
    filterProducts();
});

setInterval(updateCountdowns, 1000);

function showLoginModal() {
    var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    loginModal.show();
}
<?php foreach ($flashProducts as $product): ?>
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
</body>
</html> 