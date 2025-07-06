<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Supplier Details - Hanouty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
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
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html> 