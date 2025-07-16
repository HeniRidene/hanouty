<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../../controller/UserController.php';
$userController = new UserController();
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'role' => 'client', // Only client registration from front office
        'address' => $_POST['address'] ?? '',
        'phone' => $_POST['phone'] ?? ''
    ];
    $result = $userController->createUser($data);
    if ($result['success']) {
        header('Location: router.php?action=login&registered=1');
        exit();
    } else {
        $message = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Hanouty</title>
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="mb-4 text-center">Register to Hanouty</h2>
                        <form method="POST" action="/hanouty/view/front_office/router.php?action=register">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address">
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                            <?php if (!empty($message)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo htmlspecialchars($message); ?>
                                </div>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-success w-100">Register</button>
                        </form>
                        <div class="text-center mt-3">
                            <p>Already have an account? <a href="/hanouty/view/front_office/router.php?action=login">Login here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Footer-->
<footer class="py-5 bg-dark">
    <div class="container">
        <p class="m-0 text-center text-white">Copyright &copy; Hanouty 2025</p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 