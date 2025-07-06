<?php
session_start();
require_once __DIR__ . '/../model/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // Handle login
    public function login($email, $password) {
        try {
            // Validate input
            if (empty($email) || empty($password)) {
                return ['success' => false, 'message' => 'Email and password are required'];
            }

            // Authenticate user
            $user = $this->userModel->authenticate($email, $password);
            
            if ($user) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['logged_in'] = true;

                return ['success' => true, 'message' => 'Login successful', 'user' => $user];
            } else {
                return ['success' => false, 'message' => 'Invalid email or password'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Login error: ' . $e->getMessage()];
        }
    }

    // Handle logout
    public function logout() {
        // Destroy session
        session_destroy();
        return ['success' => true, 'message' => 'Logout successful'];
    }

    // Check if user is logged in
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    // Check if user is admin
    public function isAdmin() {
        return $this->isLoggedIn() && $_SESSION['role'] === 'admin';
    }

    // Check if user is supplier
    public function isSupplier() {
        return $this->isLoggedIn() && $_SESSION['role'] === 'supplier';
    }

    // Check if user is client
    public function isClient() {
        return $this->isLoggedIn() && $_SESSION['role'] === 'client';
    }

    // Get current user data
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['name'],
                'email' => $_SESSION['email'],
                'role' => $_SESSION['role']
            ];
        }
        return null;
    }

    // Get current user with details
    public function getCurrentUserWithDetails() {
        if ($this->isLoggedIn()) {
            return $this->userModel->getUserWithDetails($_SESSION['user_id']);
        }
        return null;
    }

    // Require authentication
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
    }

    // Require admin access
    public function requireAdmin() {
        $this->requireAuth();
        if (!$this->isAdmin()) {
            header('Location: index.php?error=access_denied');
            exit();
        }
    }

    // Require supplier access
    public function requireSupplier() {
        $this->requireAuth();
        if (!$this->isSupplier()) {
            header('Location: index.php?error=access_denied');
            exit();
        }
    }

    // Handle login form submission
    public function handleLoginForm() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $result = $this->login($email, $password);
            
            if ($result['success']) {
                // Redirect based on user role
                switch ($result['user']['role']) {
                    case 'admin':
                        header('Location: index.php');
                        break;
                    case 'supplier':
                        header('Location: supplier_dashboard.php');
                        break;
                    case 'client':
                        header('Location: customer_dashboard.php');
                        break;
                    default:
                        header('Location: index.php');
                }
                exit();
            } else {
                return $result['message'];
            }
        }
        return null;
    }
}
?> 