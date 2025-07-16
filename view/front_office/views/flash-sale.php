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
        .product-card { transition: transform 0.3s; }
        .product-card:hover { transform: scale(1.05); }
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
        <!-- Flash sale content here -->
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
        }
    });
}
setInterval(updateCountdowns, 1000);
document.addEventListener('DOMContentLoaded', updateCountdowns);
</script>
</body>
</html> 