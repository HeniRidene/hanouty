<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/hanouty/auth/config.php';

class Product {
    private $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    // Get all active products with supplier information
    public function getAllActiveProducts() {
        try {
            $sql = "SELECT p.*, u.name as supplier_name, s.business_name, s.profile_image 
                    FROM products p 
                    JOIN users u ON p.user_id = u.id 
                    LEFT JOIN supplier s ON u.id = s.user_id 
                    WHERE p.status = 'active' 
                    ORDER BY p.created_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Error getting products: " . $e->getMessage());
        }
    }

    // Get products by supplier
    public function getProductsBySupplier($supplierId) {
        try {
            $sql = "SELECT p.*, u.name as supplier_name, s.business_name, s.profile_image 
                    FROM products p 
                    JOIN users u ON p.user_id = u.id 
                    LEFT JOIN supplier s ON u.id = s.user_id 
                    WHERE p.user_id = :supplier_id AND p.status = 'active' 
                    ORDER BY p.created_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':supplier_id', $supplierId);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Error getting supplier products: " . $e->getMessage());
        }
    }

    // Get all suppliers with their products count
    public function getSuppliersWithProducts() {
        try {
            $sql = "SELECT u.id, u.name, s.business_name, s.bio, s.profile_image, s.is_verified,
                           COUNT(p.id) as products_count
                    FROM users u 
                    LEFT JOIN supplier s ON u.id = s.user_id 
                    LEFT JOIN products p ON u.id = p.user_id AND p.status = 'active'
                    WHERE u.role = 'supplier'
                    GROUP BY u.id
                    ORDER BY s.premium_rank DESC, u.created_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Error getting suppliers: " . $e->getMessage());
        }
    }

    // Get product by ID
    public function getById($id) {
        try {
            $sql = "SELECT p.*, u.name as supplier_name, s.business_name, s.bio, s.profile_image 
                    FROM products p 
                    JOIN users u ON p.user_id = u.id 
                    LEFT JOIN supplier s ON u.id = s.user_id 
                    WHERE p.id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception("Error getting product: " . $e->getMessage());
        }
    }

    // Search products
    public function searchProducts($searchTerm) {
        try {
            $sql = "SELECT p.*, u.name as supplier_name, s.business_name, s.profile_image 
                    FROM products p 
                    JOIN users u ON p.user_id = u.id 
                    LEFT JOIN supplier s ON u.id = s.user_id 
                    WHERE p.status = 'active' 
                    AND (p.title LIKE :search OR p.description LIKE :search OR s.business_name LIKE :search)
                    ORDER BY p.created_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $searchPattern = "%$searchTerm%";
            $stmt->bindParam(':search', $searchPattern);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Error searching products: " . $e->getMessage());
        }
    }
    
    // Add new product
    public function addProduct($data) {
        try {
            $sql = "INSERT INTO products (title, description, price, category, images, user_id, is_flash_sale, status, created_at) 
                    VALUES (:title, :description, :price, :category, :images, :user_id, :is_flash_sale, 'active', NOW())";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':price', $data['price']);
            $stmt->bindParam(':category', $data['category']);
            $stmt->bindParam(':images', $data['images']);
            $stmt->bindParam(':user_id', $data['user_id']); // Fix: use user_id
            $stmt->bindParam(':is_flash_sale', $data['is_flash_sale']);
            $result = $stmt->execute();
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Database error: " . $errorInfo[2]);
            }
            return $this->pdo->lastInsertId(); // Return the inserted product ID
        } catch (PDOException $e) {
            throw new Exception("Error adding product: " . $e->getMessage());
        }
    }

    // Update product
    public function updateProduct($productId, $data) {
        try {
            $fields = [
                'title' => $data['title'],
                'description' => $data['description'],
                'price' => $data['price'],
                'category' => $data['category'],
                'is_flash_sale' => $data['is_flash_sale']
            ];
            $setSql = 'title = :title, description = :description, price = :price, category = :category, is_flash_sale = :is_flash_sale';
            if (!empty($data['images'])) {
                $fields['images'] = $data['images'];
                $setSql .= ', images = :images';
            }
            $sql = "UPDATE products SET $setSql WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            foreach ($fields as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            $stmt->bindValue(':id', $productId);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error updating product: " . $e->getMessage());
        }
    }

    // Delete product by ID
    public function deleteProduct($productId) {
        try {
            $sql = "DELETE FROM products WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $productId);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error deleting product: " . $e->getMessage());
        }
    }
}
?> 