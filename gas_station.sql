CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `fuel_prices` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `fuel_type` VARCHAR(50) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_fuel_type` (`fuel_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `sales` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `fuel_type` VARCHAR(50) NOT NULL,
    `liters` DECIMAL(10,2) NOT NULL,
    `price_per_liter` DECIMAL(10,2) NOT NULL,
    `total` DECIMAL(10,2) NOT NULL,
    `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `fuel_prices` (`fuel_type`, `price`) VALUES
('Gasoline', 65.50),
('Diesel', 60.20),
('Premium', 72.90)
ON DUPLICATE KEY UPDATE
`price` = VALUES(`price`);

INSERT INTO `users` (`username`, `password`)
VALUES (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC4ZJ6rC5vF4cJ5z6v0W'
)
ON DUPLICATE KEY UPDATE
`username` = VALUES(`username`);
