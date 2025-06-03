-- Base de datos y tablas para el sistema de restaurante

CREATE DATABASE IF NOT EXISTS restaurante CHARACTER SET utf8mb4;
USE restaurante;

CREATE TABLE IF NOT EXISTS roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(30) NOT NULL
);

INSERT INTO roles (name) VALUES ('Administrator'), ('Chef'), ('Waiter'), ('Client')
ON DUPLICATE KEY UPDATE name = name;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role_id INT NOT NULL,
  FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE IF NOT EXISTS dishes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  description VARCHAR(255),
  price DECIMAL(10, 2) NOT NULL
);

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  dish_id INT NOT NULL,
  quantity INT NOT NULL,
  status ENUM('Pending', 'Preparing', 'Ready to serve', 'Served') NOT NULL DEFAULT 'Pending',
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (dish_id) REFERENCES dishes(id)
);

CREATE TABLE IF NOT EXISTS comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  comment TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  reply_to INT DEFAULT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (reply_to) REFERENCES comments(id)
);

-- Datos de ejemplo para pruebas rápidas
INSERT INTO dishes (name, description, price) VALUES
  ('Pizza Margherita', 'Clásica pizza italiana con tomate, mozzarella y albahaca.', 9.99),
  ('Ensalada César', 'Lechuga, pollo, crutones, parmesano y aderezo César.', 7.50),
  ('Lomo Saltado', 'Tiras de lomo salteadas con cebolla, tomate y papas.', 12.00)
ON DUPLICATE KEY UPDATE name = name;
DELETE FROM roles WHERE name IS NULL OR name = '';