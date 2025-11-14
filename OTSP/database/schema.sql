-- OTSP database schema

-- Create database (you can rename otsp_db if you want)
CREATE DATABASE IF NOT EXISTS otsp_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE otsp_db;

-- Categories
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  category_slug VARCHAR(100) NOT NULL,
  name VARCHAR(200) NOT NULL,
  slug VARCHAR(200) NOT NULL,
  description TEXT,
  cpu VARCHAR(100),
  ram VARCHAR(100),
  storage VARCHAR(100),
  gpu VARCHAR(100),
  display VARCHAR(100),
  os VARCHAR(100),
  price DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  image VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_products_category
    FOREIGN KEY (category_id) REFERENCES categories(id)
      ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders
CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(200) NOT NULL,
  email VARCHAR(200) NOT NULL,
  phone VARCHAR(50) NOT NULL,
  address TEXT NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order items
CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  unit_price DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_order_items_order
    FOREIGN KEY (order_id) REFERENCES orders(id)
      ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_order_items_product
    FOREIGN KEY (product_id) REFERENCES products(id)
      ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin users
CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample categories
INSERT INTO categories (name, slug) VALUES
  ('Laptops', 'laptops'),
  ('Desktops', 'desktops'),
  ('Components', 'components'),
  ('Peripherals', 'peripherals');

-- Default admin user (username: admin, password: 123)
INSERT INTO admins (username, password)
VALUES ('admin', '123')
ON DUPLICATE KEY UPDATE username = username;

-- Sample products
INSERT INTO products
  (category_id, category_slug, name, slug, description, cpu, ram, storage, gpu, display, os, price, stock, image)
VALUES
  (1, 'laptops', 'UltraBook Pro 14', 'ultrabook-pro-14',
   'Lightweight 14" laptop with Intel i7, 16GB RAM, 512GB SSD.',
   'Intel Core i7', '16GB DDR4', '512GB SSD', 'Integrated graphics', '14" IPS 1920x1080', 'Windows 11',
   899.00, 10, 'laptop1.jpg'),
  (1, 'laptops', 'Gaming Laptop X15', 'gaming-laptop-x15',
   '15.6" gaming laptop with RTX graphics and 144Hz display.',
   'Intel Core i7', '16GB', '1TB SSD', 'NVIDIA RTX 3060', '15.6" 144Hz IPS', 'Windows 11',
   1299.00, 5, 'laptop2.jpg'),
  (2, 'desktops', 'Gaming Desktop RTX', 'gaming-desktop-rtx',
   'High-performance desktop with RTX GPU for modern games.',
   'Intel Core i9', '32GB', '1TB SSD', 'NVIDIA RTX 3080', '-', 'Windows 11',
   1499.00, 3, 'desktop1.jpg'),
  (3, 'components', '16GB DDR4 RAM Kit', '16gb-ddr4-ram-kit',
   'Dual-channel 16GB (2x8GB) DDR4 3200MHz memory kit.',
   '-', '16GB DDR4 (2x8GB)', '-', '-', '-', '-',
   79.99, 25, 'ram1.jpg'),
  (3, 'components', '1TB NVMe SSD', '1tb-nvme-ssd',
   'High-speed 1TB NVMe SSD for fast boot and load times.',
   '-', '-', '1TB NVMe SSD', '-', '-', '-',
   129.99, 20, 'ssd1.jpg'),
  (4, 'peripherals', 'Mechanical Keyboard RGB', 'mechanical-keyboard-rgb',
   'Full-size mechanical keyboard with RGB backlighting.',
   '-', '-', '-', '-', '-', '-',
   99.99, 15, 'keyboard1.jpg');