-- Tabela z daniami
CREATE TABLE IF NOT EXISTS `dishes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela powiązań restauracja-danie
CREATE TABLE IF NOT EXISTS `restaurant_dishes` (
  `restaurant_id` INT(11) NOT NULL,
  `dish_id` INT(11) NOT NULL,
  PRIMARY KEY (`restaurant_id`, `dish_id`),
  FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`dish_id`) REFERENCES `dishes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Przykładowe dania
INSERT INTO `dishes` (`name`) VALUES
('Pizza Margherita'),
('Burger'),
('Sushi'),
('Pad Thai'),
('Pierogi'),
('Kebab'),
('Sałatka Cezar'),
('Zupa pomidorowa'),
('Tatar'),
('Schabowy');

-- Przykładowe powiązania restauracji z daniami
INSERT INTO `restaurant_dishes` (`restaurant_id`, `dish_id`) VALUES
(1, 1), (1, 2), (1, 5),
(2, 2), (2, 6), (2, 7),
(3, 3), (3, 4), (3, 8),
(4, 1), (4, 9), (4, 10),
(5, 2), (5, 3), (5, 5),
(6, 4), (6, 6), (6, 7),
(7, 1), (7, 8), (7, 10),
(8, 2), (8, 3), (8, 9),
(9, 4), (9, 5), (9, 6),
(10, 7), (10, 8), (10, 9);
