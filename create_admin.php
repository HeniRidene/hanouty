<?php
require_once 'auth/config.php';

try {
    $pdo = config::getConnexion();
    
    // Check if admin user already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute(['admin@hanouty.com']);
    
    if ($stmt->fetchColumn() == 0) {
        // Create admin user
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        $name = 'Admin User';
        $email = 'admin@hanouty.com';
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $role = 'admin';
        
        $stmt->execute([$name, $email, $password, $role]);
        
        echo "Admin user created successfully!\n";
        echo "Email: admin@hanouty.com\n";
        echo "Password: admin123\n";
    } else {
        echo "Admin user already exists!\n";
        echo "Email: admin@hanouty.com\n";
        echo "Password: admin123\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 