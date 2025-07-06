<?php
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/AuthController.php';

class UserController {
    private $userModel;
    private $authController;

    public function __construct() {
        $this->userModel = new User();
        $this->authController = new AuthController();
    }

    // Create new user
    public function createUser($data) {
        try {
            // Validate required fields
            $requiredFields = ['name', 'email', 'password', 'role'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return ['success' => false, 'message' => ucfirst($field) . ' is required'];
                }
            }

            // Validate email format
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Invalid email format'];
            }

            // Check if email already exists
            if ($this->userModel->emailExists($data['email'])) {
                return ['success' => false, 'message' => 'Email already exists'];
            }

            // Validate password strength
            if (strlen($data['password']) < 6) {
                return ['success' => false, 'message' => 'Password must be at least 6 characters long'];
            }

            // Create user
            $result = $this->userModel->create($data);
            
            if ($result) {
                return ['success' => true, 'message' => 'User created successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to create user'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error creating user: ' . $e->getMessage()];
        }
    }

    // Get all users
    public function getAllUsers() {
        try {
            return $this->userModel->getAll();
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Get all users with details
    public function getAllUsersWithDetails() {
        try {
            return $this->userModel->getAllWithDetails();
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Get user by ID
    public function getUserById($id) {
        try {
            return $this->userModel->getById($id);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Get user with details
    public function getUserWithDetails($id) {
        try {
            return $this->userModel->getUserWithDetails($id);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Update user
    public function updateUser($id, $data) {
        try {
            // Validate required fields
            $requiredFields = ['name', 'email', 'role'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return ['success' => false, 'message' => ucfirst($field) . ' is required'];
                }
            }

            // Validate email format
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Invalid email format'];
            }

            // Check if email already exists (excluding current user)
            if ($this->userModel->emailExists($data['email'], $id)) {
                return ['success' => false, 'message' => 'Email already exists'];
            }

            // Update user
            $result = $this->userModel->update($id, $data);
            
            if ($result) {
                // Update additional details based on role
                if ($data['role'] === 'supplier') {
                    $this->userModel->updateSupplier($id, $data);
                } elseif ($data['role'] === 'client') {
                    $this->userModel->updateClient($id, $data);
                }
                
                return ['success' => true, 'message' => 'User updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update user'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error updating user: ' . $e->getMessage()];
        }
    }

    // Delete user
    public function deleteUser($id) {
        try {
            // Check if user exists
            $user = $this->userModel->getById($id);
            if (!$user) {
                return ['success' => false, 'message' => 'User not found'];
            }

            // Prevent deleting admin users
            if ($user['role'] === 'admin') {
                return ['success' => false, 'message' => 'Cannot delete admin users'];
            }

            $result = $this->userModel->delete($id);
            
            if ($result) {
                return ['success' => true, 'message' => 'User deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to delete user'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error deleting user: ' . $e->getMessage()];
        }
    }

    // Update password
    public function updatePassword($id, $newPassword) {
        try {
            // Validate password strength
            if (strlen($newPassword) < 6) {
                return ['success' => false, 'message' => 'Password must be at least 6 characters long'];
            }

            $result = $this->userModel->updatePassword($id, $newPassword);
            
            if ($result) {
                return ['success' => true, 'message' => 'Password updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update password'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error updating password: ' . $e->getMessage()];
        }
    }

    // Search users
    public function searchUsers($searchTerm) {
        try {
            if (empty($searchTerm)) {
                return $this->getAllUsers();
            }
            return $this->userModel->search($searchTerm);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Get users by role
    public function getUsersByRole($role) {
        try {
            return $this->userModel->getByRole($role);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Get suppliers
    public function getSuppliers() {
        try {
            return $this->userModel->getSuppliers();
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Get clients
    public function getClients() {
        try {
            return $this->userModel->getClients();
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Handle user form submission (create/update)
    public function handleUserForm() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            $userData = [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'role' => $_POST['role'] ?? 'client',
                'business_name' => trim($_POST['business_name'] ?? ''),
                'bio' => trim($_POST['bio'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'phone' => trim($_POST['phone'] ?? '')
            ];

            if ($action === 'create') {
                $userData['password'] = $_POST['password'] ?? '';
                return $this->createUser($userData);
            } elseif ($action === 'update') {
                $id = $_POST['user_id'] ?? '';
                if (empty($id)) {
                    return ['success' => false, 'message' => 'User ID is required for update'];
                }
                return $this->updateUser($id, $userData);
            }
        }
        return null;
    }

    // Handle password update form
    public function handlePasswordForm() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['user_id'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($id)) {
                return ['success' => false, 'message' => 'User ID is required'];
            }

            if (empty($newPassword)) {
                return ['success' => false, 'message' => 'New password is required'];
            }

            if ($newPassword !== $confirmPassword) {
                return ['success' => false, 'message' => 'Passwords do not match'];
            }

            return $this->updatePassword($id, $newPassword);
        }
        return null;
    }

    // Handle user deletion
    public function handleDeleteUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['user_id'] ?? '';
            
            if (empty($id)) {
                return ['success' => false, 'message' => 'User ID is required'];
            }

            return $this->deleteUser($id);
        }
        return null;
    }
}
?> 