<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/hanouty/auth/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($userRole)) {
    $userRole = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'supplier') ? 'supplier' : null;
}

class User {
    private $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    // Create a new user
    public function create($data) {
        try {
            $sql = "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)";
            
            $stmt = $this->pdo->prepare($sql);
            
            // Hash the password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':role', $data['role']);
            
            $result = $stmt->execute();
            
            if ($result && $data['role'] === 'supplier') {
                // Create supplier record
                $userId = $this->pdo->lastInsertId();
                $this->createSupplier($userId, $data);
            } elseif ($result && $data['role'] === 'client') {
                // Create client record
                $userId = $this->pdo->lastInsertId();
                $this->createClient($userId, $data);
            }
            
            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error creating user: " . $e->getMessage());
        }
    }

    // Create supplier record
    private function createSupplier($userId, $data) {
        try {
            $sql = "INSERT INTO supplier (user_id, business_name, bio) VALUES (:user_id, :business_name, :bio)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':business_name', $data['business_name'] ?? '');
            $stmt->bindParam(':bio', $data['bio'] ?? '');
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error creating supplier: " . $e->getMessage());
        }
    }

    // Create client record
    private function createClient($userId, $data) {
        try {
            $sql = "INSERT INTO client (user_id, address, phone) VALUES (:user_id, :address, :phone)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':address', $data['address'] ?? '');
            $stmt->bindParam(':phone', $data['phone'] ?? '');
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error creating client: " . $e->getMessage());
        }
    }

    // Get user by ID
    public function getById($id) {
        try {
            $sql = "SELECT * FROM users WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception("Error getting user: " . $e->getMessage());
        }
    }

    // Get user by email
    public function getByEmail($email) {
        try {
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception("Error getting user: " . $e->getMessage());
        }
    }

    // Get user with additional info (supplier/client details)
    public function getUserWithDetails($id) {
        try {
            $sql = "SELECT u.*, 
                    s.business_name, s.bio, s.profile_image, s.premium_rank, s.premium_expiry, s.is_verified,
                    c.address, c.phone
                    FROM users u 
                    LEFT JOIN supplier s ON u.id = s.user_id 
                    LEFT JOIN client c ON u.id = c.user_id 
                    WHERE u.id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception("Error getting user details: " . $e->getMessage());
        }
    }

    // Get all users
    public function getAll() {
        try {
            $sql = "SELECT * FROM users ORDER BY created_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Error getting users: " . $e->getMessage());
        }
    }

    // Get all users with details
    public function getAllWithDetails() {
        try {
            $sql = "SELECT u.*, 
                    s.business_name, s.bio, s.profile_image, s.premium_rank, s.premium_expiry, s.is_verified,
                    c.address, c.phone
                    FROM users u 
                    LEFT JOIN supplier s ON u.id = s.user_id 
                    LEFT JOIN client c ON u.id = c.user_id 
                    ORDER BY u.created_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Error getting users: " . $e->getMessage());
        }
    }

    // Update user
    public function update($id, $data) {
        try {
            $sql = "UPDATE users SET name = :name, email = :email, role = :role WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':role', $data['role']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error updating user: " . $e->getMessage());
        }
    }

    // Update supplier details
    public function updateSupplier($userId, $data) {
        try {
            $sql = "UPDATE supplier SET business_name = :business_name, bio = :bio WHERE user_id = :user_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':business_name', $data['business_name'] ?? '');
            $stmt->bindParam(':bio', $data['bio'] ?? '');
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error updating supplier: " . $e->getMessage());
        }
    }

    // Update client details
    public function updateClient($userId, $data) {
        try {
            $sql = "UPDATE client SET address = :address, phone = :phone WHERE user_id = :user_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':address', $data['address'] ?? '');
            $stmt->bindParam(':phone', $data['phone'] ?? '');
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error updating client: " . $e->getMessage());
        }
    }

    // Update password
    public function updatePassword($id, $newPassword) {
        try {
            $sql = "UPDATE users SET password = :password WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error updating password: " . $e->getMessage());
        }
    }

    // Delete user
    public function delete($id) {
        try {
            $sql = "DELETE FROM users WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error deleting user: " . $e->getMessage());
        }
    }

    // Authenticate user
    public function authenticate($email, $password) {
        try {
            $user = $this->getByEmail($email);
            if ($user && password_verify($password, $user['password'])) {
                return $user;
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Error authenticating user: " . $e->getMessage());
        }
    }

    // Check if email exists
    public function emailExists($email, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
            if ($excludeId) {
                $sql .= " AND id != :exclude_id";
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':email', $email);
            if ($excludeId) {
                $stmt->bindParam(':exclude_id', $excludeId);
            }
            
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception("Error checking email: " . $e->getMessage());
        }
    }

    // Get users by role
    public function getByRole($role) {
        try {
            $sql = "SELECT * FROM users WHERE role = :role ORDER BY created_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':role', $role);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Error getting users by role: " . $e->getMessage());
        }
    }

    // Search users
    public function search($searchTerm) {
        try {
            $sql = "SELECT * FROM users WHERE 
                    name LIKE :search OR 
                    email LIKE :search 
                    ORDER BY created_at DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $searchTerm = "%$searchTerm%";
            $stmt->bindParam(':search', $searchTerm);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Error searching users: " . $e->getMessage());
        }
    }

    // Get suppliers
    public function getSuppliers() {
        try {
            $sql = "SELECT u.*, s.business_name, s.bio, s.profile_image, s.premium_rank, s.premium_expiry, s.is_verified
                    FROM users u 
                    INNER JOIN supplier s ON u.id = s.user_id 
                    ORDER BY u.created_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Error getting suppliers: " . $e->getMessage());
        }
    }

    // Get clients
    public function getClients() {
        try {
            $sql = "SELECT u.*, c.address, c.phone
                    FROM users u 
                    INNER JOIN client c ON u.id = c.user_id 
                    ORDER BY u.created_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Error getting clients: " . $e->getMessage());
        }
    }

    function getFeaturedSuppliers($page) {
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT s.*, f.spot_number
                FROM supplier s
                JOIN featured_spots f ON s.user_id = f.supplier_id
                WHERE f.page_number = :page AND f.start_date <= :now AND f.end_date >= :now
                ORDER BY f.spot_number ASC";
        // fetch and return
    }

    function getRegularSuppliers($excludeSupplierIds, $limit) {
        $sql = "SELECT * FROM supplier WHERE user_id NOT IN (...) LIMIT $limit";
        // fetch and return
    }
}

$spotPrices = [
    1 => 100, // 1st spot: 100 DT
    2 => 90,
    3 => 80,
    4 => 70,
    5 => 60,
    6 => 50,
    7 => 40,
    8 => 30,
    9 => 20,
    10 => 10
];
// For page 2, 3, etc., you can use lower prices.
?> 

<style>
.featured-products {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
.product-widget {
    width: 100%;
    border: 1px solid #eee;
    border-radius: 8px;
    padding: 1rem;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    position: relative;
}
.badge-premium {
    background: #ffd700;
    color: #333;
    padding: 0.3em 0.7em;
    border-radius: 5px;
    font-weight: bold;
    position: absolute;
    top: 1rem;
    right: 1rem;
}
.badge-secondary {
    background: #eee;
    color: #888;
    padding: 0.3em 0.7em;
    border-radius: 5px;
}
</style> 