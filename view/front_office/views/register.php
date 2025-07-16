<?php
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
</head>
<body>
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
</body>
</html> 