<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Hanouty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <li class="nav-item"><a class="nav-link text-white" href="/hanouty/view/front_office/router.php?action=cart"><i class="bi bi-cart"></i> Cart</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="nav-link text-white" href="/hanouty/view/front_office/router.php?action=profile">Profile</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="container py-5">
    <h2 class="mb-4">Your Cart</h2>
    <?php if (empty($products)): ?>
        <div class="alert alert-info">Your cart is empty.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $subtotal = 0; ?>
                    <?php foreach ($products as $product): ?>
                        <?php $lineTotal = $product['price'] * $product['quantity']; $subtotal += $lineTotal; ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?= $product['images'] ? json_decode($product['images'], true)[0] : 'https://dummyimage.com/80x80/dee2e6/6c757d.jpg' ?>" alt="<?= htmlspecialchars($product['title']) ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 0.5rem; margin-right: 12px;">
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($product['title']) ?></div>
                                        <small class="text-muted">ID: <?= $product['id'] ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?= number_format($product['price'], 2) ?> DT</td>
                            <td><?= $product['quantity'] ?></td>
                            <td><?= number_format($lineTotal, 2) ?> DT</td>
                            <td>
                                <a href="router.php?action=remove-from-cart&id=<?= $product['id'] ?>" class="btn btn-danger btn-sm" title="Remove"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex flex-column align-items-end">
            <div class="mb-2"><strong>Subtotal:</strong> <?= number_format($subtotal, 2) ?> DT</div>
            <div class="mb-2"><strong>Delivery Fee:</strong> <?= number_format($deliveryFee, 2) ?> DT</div>
            <div class="fs-4 mb-4"><strong>Total:</strong> <?= number_format($subtotal + $deliveryFee, 2) ?> DT</div>
            <button class="btn btn-success btn-lg" disabled>Checkout (Coming Soon)</button>
        </div>
    <?php endif; ?>
</main>
<footer class="py-5 bg-dark mt-5">
    <div class="container">
        <p class="m-0 text-center text-white">Copyright &copy; Hanouty 2025</p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 