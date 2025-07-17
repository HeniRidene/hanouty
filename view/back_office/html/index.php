<?php
require_once '../../../controller/AuthController.php';
require_once '../../../controller/UserController.php';

$authController = new AuthController();
$userController = new UserController();

// Check if user is logged in
if (!$authController->isLoggedIn()) {
    header('Location: authentication-login.php');
    exit();
}

// Check if user is admin
if (!$authController->isAdmin()) {
    $currentUser = $authController->getCurrentUser();
    $message = "Welcome " . $currentUser['name'] . "! You don't have admin access.";
}

// Handle form submissions
$message = '';
$messageType = '';

// Handle user creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $result = $userController->handleUserForm();
    if ($result) {
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    }
}

// Handle user update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $result = $userController->handleUserForm();
    if ($result) {
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    }
}

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $result = $userController->handleDeleteUser();
    if ($result) {
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    }
}

// Get all users with details
$users = $userController->getAllUsersWithDetails();
if (isset($users['error'])) {
    $message = $users['error'];
    $messageType = 'danger';
    $users = [];
}

$currentUser = $authController->getCurrentUser();
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hanouty Admin Dashboard</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>



    </div>
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <!-- Sidebar scroll-->
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href="./index.php" class="text-nowrap logo-img">
            <img src="../assets/images/logos/logo.svg" alt="" />
          </a>
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-6"></i>
          </div>
        </div>
        <!-- Sidebar navigation-->
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link" href="index.php">
              <i class="ti ti-home"></i> Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="products.php">
              <i class="ti ti-package"></i> Products
            </a>
          </li>
          <!-- ... other sidebar items ... -->
        </ul>
        <!-- End Sidebar navigation -->
      </div>
      <!-- End Sidebar scroll-->
    </aside>
    <!--  Sidebar End -->
    <!--  Main wrapper -->
    <div class="body-wrapper">
      <!--  Header Start -->
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
              <a class="nav-link sidebartoggler " id="headerCollapse" href="javascript:void(0)">
                <i class="ti ti-menu-2"></i>
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link " href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ti ti-bell"></i>
                <div class="notification bg-primary rounded-circle"></div>
              </a>
              <div class="dropdown-menu dropdown-menu-animate-up" aria-labelledby="drop1">
                <div class="message-body">
                  <a href="javascript:void(0)" class="dropdown-item">
                    Item 1
                  </a>
                  <a href="javascript:void(0)" class="dropdown-item">
                    Item 2
                  </a>
                </div>
              </div>
            </li>
          </ul>
          <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">

              <li class="nav-item dropdown">
                <a class="nav-link " href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <img src="../assets/images/profile/user-1.jpg" alt="" width="35" height="35" class="rounded-circle">
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                  <div class="message-body">
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-user fs-6"></i>
                      <p class="mb-0 fs-3"><?php echo htmlspecialchars($currentUser['name']); ?></p>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-mail fs-6"></i>
                      <p class="mb-0 fs-3"><?php echo htmlspecialchars($currentUser['email']); ?></p>
                    </a>
                    <a href="logout.php" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <!--  Header End -->
      <div class="body-wrapper-inner">
        <div class="container-fluid">
          <!--  Row 1 -->
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold">User Management</h5>
                    <?php if ($authController->isAdmin()): ?>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                      <i class="ti ti-plus"></i> Add User
                    </button>
                    <?php endif; ?>
                  </div>

                  <?php if (!empty($message)): ?>
                  <div class="alert alert-<?php echo $messageType; ?>" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                  </div>
                  <?php endif; ?>

                  <?php if ($authController->isAdmin()): ?>
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Name</th>
                          <th>Email</th>
                          <th>Role</th>
                          <th>Business/Address</th>
                          <th>Created</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                          <td><?php echo $user['id']; ?></td>
                          <td><?php echo htmlspecialchars($user['name']); ?></td>
                          <td><?php echo htmlspecialchars($user['email']); ?></td>
                          <td>
                            <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'supplier' ? 'warning' : 'info'); ?>">
                              <?php echo ucfirst($user['role']); ?>
                            </span>
                          </td>
                          <td>
                            <?php if ($user['role'] === 'supplier' && !empty($user['business_name'])): ?>
                              <?php echo htmlspecialchars($user['business_name']); ?>
                            <?php elseif ($user['role'] === 'client' && !empty($user['address'])): ?>
                              <?php echo htmlspecialchars($user['address']); ?>
                            <?php else: ?>
                              -
                            <?php endif; ?>
                          </td>
                          <td><?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?></td>
                          <td>
                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                    onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                              <i class="ti ti-edit"></i>
                            </button>
                            <?php if ($user['role'] !== 'admin'): ?>
                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                    onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['name']); ?>')">
                              <i class="ti ti-trash"></i>
                            </button>
                            <?php endif; ?>
                          </td>
                        </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                  <?php else: ?>
                  <div class="alert alert-info">
                    <?php echo htmlspecialchars($message); ?>
                  </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add User Modal -->
  <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="">
          <div class="modal-body">
            <input type="hidden" name="action" value="create">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="name" class="form-label">Name</label>
                  <input type="text" class="form-control" id="name" name="name" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" id="email" name="email" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="password" class="form-label">Password</label>
                  <input type="password" class="form-control" id="password" name="password" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="role" class="form-label">Role</label>
                  <select class="form-control" id="role" name="role" required>
                    <option value="client">Client</option>
                    <option value="supplier">Supplier</option>
                    <option value="admin">Admin</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3" id="business_name_field" style="display:none;">
                  <label for="business_name" class="form-label">Business Name</label>
                  <input type="text" class="form-control" id="business_name" name="business_name">
                </div>
                <div class="mb-3" id="address_field" style="display:none;">
                  <label for="address" class="form-label">Address</label>
                  <input type="text" class="form-control" id="address" name="address">
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3" id="bio_field" style="display:none;">
                  <label for="bio" class="form-label">Bio</label>
                  <textarea class="form-control" id="bio" name="bio"></textarea>
                </div>
                <div class="mb-3" id="phone_field" style="display:none;">
                  <label for="phone" class="form-label">Phone</label>
                  <input type="text" class="form-control" id="phone" name="phone">
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Add User</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit User Modal -->
  <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="">
          <div class="modal-body">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="user_id" id="edit_user_id">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="edit_name" class="form-label">Name</label>
                  <input type="text" class="form-control" id="edit_name" name="name" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="edit_email" class="form-label">Email</label>
                  <input type="email" class="form-control" id="edit_email" name="email" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="edit_role" class="form-label">Role</label>
                  <select class="form-control" id="edit_role" name="role" required>
                    <option value="client">Client</option>
                    <option value="supplier">Supplier</option>
                    <option value="admin">Admin</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3" id="edit_business_name_field" style="display:none;">
                  <label for="edit_business_name" class="form-label">Business Name</label>
                  <input type="text" class="form-control" id="edit_business_name" name="business_name">
                </div>
                <div class="mb-3" id="edit_address_field" style="display:none;">
                  <label for="edit_address" class="form-label">Address</label>
                  <input type="text" class="form-control" id="edit_address" name="address">
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3" id="edit_bio_field" style="display:none;">
                  <label for="edit_bio" class="form-label">Bio</label>
                  <textarea class="form-control" id="edit_bio" name="bio"></textarea>
                </div>
                <div class="mb-3" id="edit_phone_field" style="display:none;">
                  <label for="edit_phone" class="form-label">Phone</label>
                  <input type="text" class="form-control" id="edit_phone" name="phone">
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Update User</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Delete User Modal -->
  <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteUserModalLabel">Delete User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="">
          <div class="modal-body">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="user_id" id="delete_user_id">
            <p>Are you sure you want to delete user: <strong id="delete_user_name"></strong>?</p>
            <p class="text-danger">This action cannot be undone.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Delete</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/sidebarmenu.js"></script>
  <script src="../assets/js/app.min.js"></script>
  <script src="../assets/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="../assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="../assets/js/dashboard.js"></script>
  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  <script>
    // Show/hide fields based on role selection
    document.getElementById('role').addEventListener('change', function() {
      const role = this.value;
      document.getElementById('business_name_field').style.display = role === 'supplier' ? '' : 'none';
      document.getElementById('bio_field').style.display = role === 'supplier' ? '' : 'none';
      document.getElementById('address_field').style.display = role === 'client' ? '' : 'none';
      document.getElementById('phone_field').style.display = role === 'client' ? '' : 'none';
    });

    document.getElementById('edit_role').addEventListener('change', function() {
      const role = this.value;
      document.getElementById('edit_business_name_field').style.display = role === 'supplier' ? '' : 'none';
      document.getElementById('edit_bio_field').style.display = role === 'supplier' ? '' : 'none';
      document.getElementById('edit_address_field').style.display = role === 'client' ? '' : 'none';
      document.getElementById('edit_phone_field').style.display = role === 'client' ? '' : 'none';
    });

    // Edit user function
    function editUser(user) {
      document.getElementById('edit_user_id').value = user.id;
      document.getElementById('edit_name').value = user.name;
      document.getElementById('edit_email').value = user.email;
      document.getElementById('edit_role').value = user.role;
      
      if (user.role === 'supplier') {
        document.getElementById('edit_business_name').value = user.business_name || '';
        document.getElementById('edit_bio').value = user.bio || '';
        document.getElementById('edit_business_name_field').style.display = '';
        document.getElementById('edit_bio_field').style.display = '';
        document.getElementById('edit_address_field').style.display = 'none';
        document.getElementById('edit_phone_field').style.display = 'none';
      } else if (user.role === 'client') {
        document.getElementById('edit_address').value = user.address || '';
        document.getElementById('edit_phone').value = user.phone || '';
        document.getElementById('edit_address_field').style.display = '';
        document.getElementById('edit_phone_field').style.display = '';
        document.getElementById('edit_business_name_field').style.display = 'none';
        document.getElementById('edit_bio_field').style.display = 'none';
      }
      
      new bootstrap.Modal(document.getElementById('editUserModal')).show();
    }

    // Delete user function
    function deleteUser(userId, userName) {
      document.getElementById('delete_user_id').value = userId;
      document.getElementById('delete_user_name').textContent = userName;
      new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
    }
  </script>
</body>

</html> 