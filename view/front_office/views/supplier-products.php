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
    <title>Supplier Products - Hanouty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body>
    <div class="container py-5">
        <a href="router.php" class="btn btn-outline-dark mb-4">&larr; Back to Home</a>
        <div class="mb-4">
            <h2 class="fw-bold mb-1">Products by <?= htmlspecialchars($supplier['business_name'] ?: $supplier['name']) ?></h2>
            <?php if ($supplier['bio']): ?>
                <div class="text-muted mb-2"> <?= htmlspecialchars($supplier['bio']) ?> </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $idx => $product): ?>
                <div class="featured-spot-product p-4 d-flex flex-column flex-md-row align-items-center gap-4 mb-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 1rem; box-shadow: 0 6px 24px rgba(25,135,84,0.08);">
                    <?php $images = $product['images'] ? json_decode($product['images'], true) : []; ?>
                    <?php if (!empty($images)): ?>
                        <?php $carouselId = 'supplier-carousel-' . $product['id'] . '-' . $idx; ?>
                        <div id="<?= $carouselId ?>" class="carousel slide flex-shrink-0" data-bs-ride="carousel" style="width: 340px; max-width: 100%;">
                            <div class="carousel-inner rounded-3 shadow">
                                <?php foreach ($images as $imgIdx => $img): ?>
                                    <div class="carousel-item<?= $imgIdx === 0 ? ' active' : '' ?>">
                                        <img src="<?= htmlspecialchars($img) ?>" class="d-block w-100" style="height: 260px; object-fit: cover; border-radius: 1rem;" alt="Product Image <?= $imgIdx + 1 ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($images) > 1): ?>
                                <button class="carousel-control-prev" type="button" data-bs-target="#<?= $carouselId ?>" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#<?= $carouselId ?>" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <img src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" class="d-block w-100" style="width:340px; height:260px; object-fit:cover; border-radius:1rem;" alt="No Image">
                    <?php endif; ?>
                    <div class="flex-grow-1">
                        <h3 class="fw-bold mb-2" style="font-size: 2rem; color: #198754; letter-spacing: -1px;">
                            <?= htmlspecialchars($product['title']) ?>
                        </h3>
                        <p class="mb-2" style="font-size: 1.1rem; color: #444; min-height: 32px;"> <?= htmlspecialchars($product['description']) ?> </p>
                        <div class="d-flex align-items-center mb-3">
                            <span class="fs-4 fw-bold text-success me-3"><?= htmlspecialchars(number_format($product['price'], 2)) ?> DT</span>
                        </div>
                        <div class="product-list-btns">
                            <a href="router.php?action=product&id=<?= $product['id'] ?>" class="btn btn-outline-dark btn-lg">View Details</a>
                            <form method="POST" action="router.php?action=add-to-cart&id=<?= $product['id'] ?>" style="display:inline-block; margin-left:8px;">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-success btn-lg">Buy</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">This supplier has no products yet.</div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 