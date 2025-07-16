<?php
require_once __DIR__ . '/../../../controller/AuthController.php';
$authController = new AuthController();
$errorMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errorMessage = $authController->handleLoginForm();
}
if ($authController->isLoggedIn()) {
    header('Location: index.php'); // Redirect to front office home
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hanouty</title>
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="mb-4 text-center">Login to Hanouty</h2>
                        <form method="POST" action="/hanouty/view/front_office/router.php?action=login">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <?php if (!empty($errorMessage)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo htmlspecialchars($errorMessage); ?>
                                </div>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-success w-100">Login</button>
                        </form>
                        <div class="text-center mt-3">
                            <p>Don't have an account? <a href="/hanouty/view/front_office/router.php?action=register">Register here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 