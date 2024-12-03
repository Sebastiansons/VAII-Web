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