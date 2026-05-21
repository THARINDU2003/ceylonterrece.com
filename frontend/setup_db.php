<?php
// setup_db.php - Run this file once to initialize the MySQL database tables
require_once 'db.php';

echo "<h2>Database Setup</h2>";

try {
    // Users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(50) DEFAULT 'user',
            account_type VARCHAR(50) DEFAULT 'personal',
            company_name VARCHAR(255),
            balance DECIMAL(10,2) DEFAULT 0.00,
            permissions TEXT DEFAULT '[]',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Users table created or already exists.<br>";

    // Properties table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS properties (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            price DECIMAL(15,2) NOT NULL,
            property_type ENUM('Land', 'House', 'Apartment', 'Commercial'),
            offer_type ENUM('Sale', 'Rent'),
            bedrooms INT,
            bathrooms INT,
            land_area DECIMAL(10,2),
            address VARCHAR(255),
            city VARCHAR(100),
            district VARCHAR(100),
            seller_name VARCHAR(255) NOT NULL,
            seller_phone VARCHAR(50) NOT NULL,
            seller_email VARCHAR(255),
            images TEXT,
            views INT DEFAULT 0,
            status VARCHAR(50) DEFAULT 'pending',
            expiry_date DATETIME,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Properties table created or already exists.<br>";

    // Inquiries table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS inquiries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            property_id INT,
            name VARCHAR(255) NOT NULL,
            phone VARCHAR(50) NOT NULL,
            email VARCHAR(255),
            message TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Inquiries table created or already exists.<br>";

    // Agents table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS agents (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255),
            phone VARCHAR(50) NOT NULL,
            whatsapp VARCHAR(50),
            photo VARCHAR(255),
            license_number VARCHAR(100),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Agents table created or already exists.<br>";

    // Construction Professionals table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS construction_professionals (
            id INT AUTO_INCREMENT PRIMARY KEY,
            company_name VARCHAR(255) NOT NULL,
            registration_number VARCHAR(100),
            location VARCHAR(255),
            category VARCHAR(100),
            phone VARCHAR(50) NOT NULL,
            email VARCHAR(255),
            portfolio_link VARCHAR(255),
            status VARCHAR(50) DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Construction Professionals table created or already exists.<br>";

    // Transfers table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS transfers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            amount DECIMAL(15,2) NOT NULL,
            bank_details TEXT,
            status VARCHAR(50) DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Transfers table created or already exists.<br>";

    // Settings table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS settings (
            `key` VARCHAR(100) PRIMARY KEY,
            `value` TEXT NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Settings table created or already exists.<br>";

    // Ad Plans table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS ad_plans (
            id VARCHAR(50) PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            duration_days INT NOT NULL,
            active TINYINT(1) DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Ad Plans table created or already exists.<br>";

    echo "<h3 style='color:green;'>Database setup completed successfully!</h3>";

} catch (PDOException $e) {
    echo "<h3 style='color:red;'>Error creating tables: " . $e->getMessage() . "</h3>";
}
?>
