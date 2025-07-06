<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Product Details - Hanouty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
    <div class="container py-5">
        <a href="javascript:history.back()" class="btn btn-outline-dark mb-4">&larr; Back</a>
        <div class="card mb-4">
            <div class="row g-0">
                <div class="col-md-5">
                    <?php 
                    $images = $product['images'] ? json_decode($product['images'], true) : [];
                    $mainImage = $images[0] ?? 'https://dummyimage.com/450x300/dee2e6/6c757d.jpg';
                    ?>
                    <img src="<?= htmlspecialchars($mainImage) ?>" class="img-fluid rounded-start w-100" alt="<?= htmlspecialchars($product['title']) ?>">
                    <?php if (count($images) > 1): ?>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <?php foreach ($images as $img): ?>
                                <img src="<?= htmlspecialchars($img) ?>" class="rounded" style="width: 60px; height: 60px; object-fit: cover;" alt="">
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
                                <div class="fw-bold">Supplier: <?= htmlspecialchars($product['business_name'] ?? $product['supplier_name'] ?? '') ?></div>
                                <?php if (!empty($product['bio'])): ?>
                                    <small class="text-muted"><?= htmlspecialchars($product['bio']) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 