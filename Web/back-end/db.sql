CREATE TABLE ShopCategories (
    CategoryID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Description VARCHAR(255),
    Icon VARCHAR(255),
    IsNew BOOLEAN DEFAULT FALSE,
    IsUnavailable BOOLEAN DEFAULT FALSE
);

INSERT INTO ShopCategories (Name, Description, Icon, IsNew, IsUnavailable) VALUES
('Sport', 'Sporting goods', 'bi-trophy', FALSE, FALSE),
('Cars', 'Automotive items', 'bi-truck', FALSE, FALSE),
('Books', 'Various books', 'bi-book', FALSE, FALSE),
('Clothes', 'Apparel and fashion', 'bi-handbag', FALSE, FALSE),
('House and garden', 'Home essentials', 'bi-house', FALSE, FALSE),
('Electro', 'Electronic devices', 'bi-tv', FALSE, FALSE),
('Mobiles', 'Smartphones', 'bi-phone', FALSE, FALSE),
('Furniture', 'Home furnishings', 'bi-lamp', FALSE, FALSE),
('PC', 'Computing devices', 'bi-laptop', FALSE, FALSE),
('Machines', 'Industrial tools', 'bi-wrench', FALSE, FALSE),
('Services', 'Various services', 'bi-tools', FALSE, FALSE),
('Music', 'Musical instruments', 'bi-music-note-beamed', FALSE, FALSE),
('Work', 'Work essentials', 'bi-briefcase', FALSE, FALSE),
('Animals', 'Pet supplies', 'bi-bug', FALSE, FALSE),
('Kids', 'Childrens items', 'bi-balloon', FALSE, FALSE),
('Others', 'Miscellaneous', 'bi-box-seam', FALSE, FALSE);

CREATE TABLE ShopItems (
    ItemID INT AUTO_INCREMENT PRIMARY KEY,
    CategoryID INT,
    Name VARCHAR(255) NOT NULL,
    Description TEXT,
    Created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Price DECIMAL(10, 2) NOT NULL,
    Image VARCHAR(255),
    FOREIGN KEY (CategoryID) REFERENCES ShopCategories(CategoryID)
);

INSERT INTO ShopItems (CategoryID, Name, Description, Price, Image) VALUES
(1, 'Basketball', 'A standard basketball for indoor and outdoor play', 29.99, 'basketball.png'),
(1, 'Soccer Ball', 'A durable soccer ball for all levels of play', 25.99, 'ball.jpg'),
(1, 'Tennis Racket', 'Lightweight tennis racket for beginners and professionals', 59.99, 'tennis_rocket.png'),
(1, 'Running Shoes', 'Comfortable running shoes with excellent grip', 79.99, 'shoes.jpg'),
(1, 'Baseball Glove', 'Leather glove for catching baseballs', 39.99, 'baseballGlove.jpg');
