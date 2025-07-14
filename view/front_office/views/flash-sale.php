<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Flash Sale - Hanouty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet" />
    <style>
        .flash-header { background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); color: white; padding: 3rem 0; text-align: center; }
        .flash-header h1 { font-size: 3rem; font-weight: bold; }
        .product-card { transition: transform 0.3s; }
        .product-card:hover { transform: scale(1.05); }
        .flash-price { color: #ff4b2b; font-weight: bold; }
    </style>
</head>
<body style="min-height: 100vh; display: flex; flex-direction: column;">
    <!-- Navigation (same as index) -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container px-4 px-lg-5">
            <a class="navbar-brand" href="/hanouty/view/front_office/router.php"><strong>Hanouty</strong></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item"><a class="nav-link" href="/hanouty/view/front_office/router.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#suppliers">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="/hanouty/view/front_office/router.php?action=common-products">Common Products</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/hanouty/view/front_office/router.php?action=flash-sale">Flash Sale</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="/hanouty/view/front_office/router.php?action=profile">Profile</a></li>
                    <?php endif; ?>
                </ul>
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

    <!-- Flash Sale Header -->
    <header class="flash-header">
        <div class="container">
            <h1>Flash Sale!</h1>
            <p class="lead">Limited time offers on amazing products</p>
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'supplier'): ?>
                <a href="router.php?action=add-product&is_flash_sale=1" class="btn btn-warning btn-lg mt-3 fw-bold shadow">+ Add Flash Sale Product</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Products Grid -->
    <section class="py-5" style="flex: 1 0 auto;">
        <div class="container px-4 px-lg-5">
            <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                <?php
                // Only show products added in the last 2 hours
                $now = time();
                $shown = 0;
                foreach ($flashProducts as $product):
                    $created = strtotime($product['created_at']);
                    if ($now - $created > 2 * 3600) continue;
                    $shown++;
                ?>
                    <div class="col mb-5">
                        <div class="card h-100 product-card shadow">
                            <img class="card-img-top" src="<?= $product['images'] ? json_decode($product['images'], true)[0] : 'https://dummyimage.com/450x300/dee2e6/6c757d.jpg' ?>" alt="<?= htmlspecialchars($product['title']) ?>" />
                            <div class="card-body p-4">
                                <div class="text-center">
                                    <h5 class="fw-bolder"><?= htmlspecialchars($product['title']) ?></h5>
                                    <div class="flash-price">$<?= number_format($product['price'], 2) ?> <small class="text-muted text-decoration-line-through">$<?= number_format($product['price'] * 1.2, 2) ?></small></div>
                                    <div class="flash-countdown mt-2" data-end="<?= strtotime($product['created_at']) + 2*3600 ?>" style="font-size:1.1rem; color:#ff4b2b; font-weight:600;"></div>
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
                <?php if ($shown === 0): ?>
                    <p class="text-center text-muted">No flash sale products available at the moment.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-5 bg-dark mt-auto">
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
        }
    });
}
setInterval(updateCountdowns, 1000);
document.addEventListener('DOMContentLoaded', updateCountdowns);
</script>
</body>
</html> 