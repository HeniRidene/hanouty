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
    <div class="container py-5">
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'supplier'): ?>
            <div class="mb-4 text-end">
                <a href="router.php?action=add-flash-sale-product" class="btn btn-warning btn-lg fw-bold shadow">
                    + Add Flash Sale Product
                </a>
            </div>
        <?php endif; ?>
        <!-- Flash sale content here -->
        <?php if (!empty($flashProducts)): ?>
            <div class="row g-4">
                <?php foreach ($flashProducts as $product): ?>
                    <div class="col-12 col-md-6 col-lg-4">
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
        } else {
            var h = Math.floor(diff / 3600000);
            var m = Math.floor((diff % 3600000) / 60000);
            var s = Math.floor((diff % 60000) / 1000);
            el.textContent = 'Ends in ' + h + 'h ' + m + 'm ' + s + 's';
            el.style.color = '#ff4b2b';
        }
    });
}
setInterval(updateCountdowns, 1000);
document.addEventListener('DOMContentLoaded', updateCountdowns);
</script>
</body>
</html> 