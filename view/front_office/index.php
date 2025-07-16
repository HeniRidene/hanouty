<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// --- DB CONNECTION (update credentials as needed) ---
$mysqli = new mysqli('localhost', 'root', '', 'hanouty');
if ($mysqli->connect_errno) {
    die('Database connection failed: ' . $mysqli->connect_error);
}

$userRole = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'supplier') ? 'supplier' : null;
$supplierId = $_SESSION['user_id'] ?? null;
$featuredPage = isset($_GET['featured_page']) ? (int)$_GET['featured_page'] : 1;
$spotPrices = [1=>100,2=>90,3=>80,4=>70,5=>60,6=>50,7=>40,8=>30,9=>20,10=>10];

// --- Pagination: count total pages ---
$res = $mysqli->query('SELECT MAX(page_number) as max_page FROM featured_spots');
$row = $res->fetch_assoc();
$totalPages = max(1, (int)$row['max_page']);

// --- Fetch all spots for this page ---
$spots = [];
$stmt = $mysqli->prepare('SELECT fs.spot_number, fs.supplier_id, fs.product_id, p.title, p.description, p.price FROM featured_spots fs LEFT JOIN products p ON fs.product_id = p.id WHERE fs.page_number = ?');
$stmt->bind_param('i', $featuredPage);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $spots[(int)$row['spot_number']] = $row;
}
$stmt->close();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Featured Product Spots</title>
    <style>
        .featured-products { display: flex; flex-direction: column; gap: 1rem; }
        .product-widget { width: 100%; border: 1px solid #eee; border-radius: 8px; padding: 1rem; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.03); position: relative; }
        .badge-premium { background: #ffd700; color: #333; padding: 0.3em 0.7em; border-radius: 5px; font-weight: bold; margin-left: 0.5em; }
        .badge-secondary { background: #eee; color: #888; padding: 0.3em 0.7em; border-radius: 5px; }
        .btn { display: inline-block; padding: 0.4em 1em; border: none; border-radius: 4px; background: #198754; color: #fff; text-decoration: none; margin-right: 0.5em; }
        .btn[disabled] { background: #aaa; }
        .pagination { margin-top: 2rem; display: flex; justify-content: center; gap: 0.5rem; }
        .pagination a, .pagination span { padding: 0.5em 1em; border: 1px solid #ccc; border-radius: 4px; text-decoration: none; color: #198754; background: #fff; }
        .pagination .active { background: #198754; color: #fff; border-color: #198754; }
    </style>
</head>
<body>
    <h2>Featured Product Spots</h2>
    <div class="featured-products">
        <?php for ($i = 1; $i <= 10; $i++): ?>
            <div class="product-widget spot-<?= $i ?>">
                <?php if (isset($spots[$i]) && $spots[$i]['supplier_id']): ?>
                    <?php if ($spots[$i]['product_id']): ?>
                        <h3><?= htmlspecialchars($spots[$i]['title']) ?></h3>
                        <p><?= htmlspecialchars($spots[$i]['description']) ?></p>
                        <span><?= htmlspecialchars($spots[$i]['price']) ?> DT</span>
                    <?php endif; ?>
                    <button class="btn" disabled>Spot Owned</button>
                    <?php if ($userRole === 'supplier' && $supplierId == $spots[$i]['supplier_id']): ?>
                        <a href="router.php?action=add-product&featured_page=<?= $featuredPage ?>&spot=<?= $i ?>" class="btn">Add a Product</a>
                    <?php endif; ?>
                <?php else: ?>
                    <h3>Available Spot #<?= $i ?></h3>
                    <?php if ($userRole === 'supplier'): ?>
                        <a href="#" class="btn buy-spot-btn" data-spot="<?= $i ?>" data-page="<?= $featuredPage ?>">
                            Buy this spot <span class="badge-premium"><?= $spotPrices[$i] ?> DT</span>
                        </a>
                    <?php else: ?>
                        <span class="badge-secondary">Available</span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endfor; ?>
    </div>
    <!-- Pagination -->
    <div class="pagination">
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <?php if ($p == $featuredPage): ?>
                <span class="active">Page <?= $p ?></span>
            <?php else: ?>
                <a href="?featured_page=<?= $p ?>">Page <?= $p ?></a>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
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
                            button.outerHTML = '<button class="btn" disabled>Spot Owned</button> ' +
                                '<a href="router.php?action=add-product&featured_page=' + page + '&spot=' + spot + '" class="btn">Add a Product</a>';
                        } else if (data === 'spot_taken') {
                            button.outerHTML = '<button class="btn" disabled>Spot Owned</button>';
                        }
                    });
            });
        });
    });
    </script>
</body>
</html> 