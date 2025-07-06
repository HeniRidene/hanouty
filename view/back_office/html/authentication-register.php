<?php
require_once '../../../controller/AuthController.php';
require_once '../../../controller/UserController.php';
$userController = new UserController();
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'role' => $_POST['role'] ?? 'client',
        'business_name' => $_POST['business_name'] ?? '',
        'bio' => $_POST['bio'] ?? '',
        'address' => $_POST['address'] ?? '',
        'phone' => $_POST['phone'] ?? ''
    ];
    $result = $userController->createUser($data);
    if ($result['success']) {
        header('Location: authentication-login.php?registered=1');
        exit();
    } else {
        $message = $result['message'];
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - Hanouty Admin</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
    <div class="position-relative overflow-hidden text-bg-light min-vh-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-md-8 col-lg-6 col-xxl-3">
            <div class="card mb-0">
              <div class="card-body">
                <a href="./index.php" class="text-nowrap logo-img text-center d-block py-3 w-100">
                  <img src="../assets/images/logos/logo.svg" alt="">
                </a>
                <p class="text-center">Register to Hanouty</p>
                <form method="POST" action="">
                  <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                  </div>
                  <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                  </div>
                  <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                  </div>
                  <div class="mb-3">
                    <label for="role" class="form-label">Register as</label>
                    <select class="form-control" id="role" name="role" required>
                      <option value="client">Client</option>
                      <option value="supplier">Supplier</option>
                    </select>
                  </div>
                  <div class="mb-3" id="supplier-fields" style="display:none;">
                    <label for="business_name" class="form-label">Business Name</label>
                    <input type="text" class="form-control" id="business_name" name="business_name">
                    <label for="bio" class="form-label mt-2">Bio</label>
                    <textarea class="form-control" id="bio" name="bio"></textarea>
                  </div>
                  <div class="mb-3" id="client-fields" style="display:none;">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address">
                    <label for="phone" class="form-label mt-2">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone">
                  </div>
                  <?php if (!empty($message)): ?>
                  <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                  </div>
                  <?php endif; ?>
                  <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Sign Up</button>
                  <div class="d-flex align-items-center justify-content-center">
                    <p class="fs-4 mb-0 fw-bold">Already have an Account?</p>
                    <a class="text-primary fw-bold ms-2" href="authentication-login.php">Sign In</a>
                  </div>
                </form>
                <script>
                  document.getElementById('role').addEventListener('change', function() {
                    document.getElementById('supplier-fields').style.display = this.value === 'supplier' ? '' : 'none';
                    document.getElementById('client-fields').style.display = this.value === 'client' ? '' : 'none';
                  });
                </script>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 