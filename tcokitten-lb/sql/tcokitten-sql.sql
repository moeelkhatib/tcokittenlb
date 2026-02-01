-- ============================================
-- CAT STORE DATABASE SETUP
-- ============================================

-- Drop database if exists (to start fresh)
DROP DATABASE IF EXISTS cat_store;

-- Create database
CREATE DATABASE IF NOT EXISTS cat_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE cat_store;

-- ============================================
-- CREATE TABLES (in correct order)
-- ============================================

-- Users table (for admin and customers) - created first
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255),
    phone VARCHAR(50),
    address TEXT,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products table
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    original_price DECIMAL(10, 2),
    category ENUM('food', 'toys', 'accessories', 'health', 'litter', 'travel') NOT NULL,
    image VARCHAR(500),
    stock INT DEFAULT 0,
    rating DECIMAL(3, 2) DEFAULT 0.00,
    reviews INT DEFAULT 0,
    tags VARCHAR(500),
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders table (needs users table first)
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id VARCHAR(50) UNIQUE NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(50),
    shipping_address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    shipping_fee DECIMAL(10, 2) DEFAULT 5.00,
    total DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_method ENUM('cash', 'card') DEFAULT 'cash',
    payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order items table (needs orders and products tables first)
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    image VARCHAR(500),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Subscribers table
CREATE TABLE subscribers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    subscribed BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contact messages table
CREATE TABLE contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cart table (needs users and products tables first)
CREATE TABLE cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    session_id VARCHAR(255),
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- INSERT SAMPLE DATA
-- ============================================

-- Insert 48 sample products (8 per category)
INSERT INTO products (name, description, price, original_price, category, image, stock, rating, reviews, tags, featured) VALUES
-- Food Category (8 products)
('Premium Dry Cat Food', 'High-protein formula for adult cats with real chicken', 25.99, 29.99, 'food', 'https://picsum.photos/300/300?random=1', 50, 4.8, 124, 'best-seller,dry-food,premium', 1),
('Wet Food Variety Pack', '24 cans of assorted flavors for kittens', 32.99, 39.99, 'food', 'https://picsum.photos/300/300?random=2', 40, 4.6, 92, 'wet-food,variety,kitten', 1),
('Grain-Free Salmon Formula', 'Limited ingredient diet for sensitive stomachs', 34.99, 39.99, 'food', 'https://picsum.photos/300/300?random=3', 30, 4.9, 67, 'grain-free,salmon,hypoallergenic', 1),
('Senior Cat Formula', 'Specially formulated for cats 7+ years', 28.99, 32.99, 'food', 'https://picsum.photos/300/300?random=4', 25, 4.7, 89, 'senior,health-support', 0),
('Kitten Growth Formula', 'Complete nutrition for growing kittens', 26.99, 30.99, 'food', 'https://picsum.photos/300/300?random=5', 60, 4.8, 156, 'kitten,growth,complete', 1),
('Organic Cat Treats', '100% natural chicken treats with no additives', 12.99, 15.99, 'food', 'https://picsum.photos/300/300?random=6', 100, 4.5, 203, 'treats,organic,chicken', 1),
('Dental Care Kibble', 'Specially shaped to reduce tartar buildup', 27.99, 32.99, 'food', 'https://picsum.photos/300/300?random=7', 45, 4.6, 78, 'dental,oral-care', 0),
('Weight Management Formula', 'Helps maintain healthy weight with fewer calories', 29.99, 34.99, 'food', 'https://picsum.photos/300/300?random=8', 35, 4.7, 92, 'weight-management,low-calorie', 1),

-- Toys Category (8 products)
('Interactive Laser Toy', 'Automatic laser pointer for endless entertainment', 19.99, 24.99, 'toys', 'https://picsum.photos/300/300?random=9', 30, 4.5, 89, 'interactive,battery-operated,laser', 1),
('Catnip Toys Set', 'Set of 3 organic catnip-filled toys', 14.99, 19.99, 'toys', 'https://picsum.photos/300/300?random=10', 100, 4.7, 156, 'organic,set,catnip', 1),
('Feather Wand Toy', 'Interactive wand with colorful feathers', 8.99, 12.99, 'toys', 'https://picsum.photos/300/300?random=11', 75, 4.4, 203, 'wand,feather,interactive', 0),
('Electronic Mouse Toy', 'Battery-powered mouse that moves randomly', 22.99, 27.99, 'toys', 'https://picsum.photos/300/300?random=12', 40, 4.6, 67, 'electronic,mouse,battery', 1),
('Crinkle Ball Pack', 'Pack of 6 colorful crinkle balls', 6.99, 9.99, 'toys', 'https://picsum.photos/300/300?random=13', 150, 4.3, 189, 'balls,crinkle,pack', 0),
('Puzzle Feeder Toy', 'Interactive toy that dispenses treats', 18.99, 24.99, 'toys', 'https://picsum.photos/300/300?random=14', 55, 4.8, 112, 'puzzle,feeder,interactive', 1),
('Tunnel Play System', 'Collapsible tunnel for hide and seek', 34.99, 39.99, 'toys', 'https://picsum.photos/300/300?random=15', 25, 4.9, 78, 'tunnel,play-system,collapsible', 1),
('Spring Toy Pack', 'Pack of 10 colorful coil springs', 7.99, 11.99, 'toys', 'https://picsum.photos/300/300?random=16', 200, 4.2, 245, 'springs,coil,pack', 0),

-- Accessories Category (8 products)
('Luxury Cat Bed', 'Plush orthopedic bed with removable cover', 45.99, 55.99, 'accessories', 'https://picsum.photos/300/300?random=17', 20, 4.9, 67, 'premium,washable,orthopedic', 1),
('Grooming Kit', 'Complete grooming set with brush and nail clippers', 28.99, 34.99, 'accessories', 'https://picsum.photos/300/300?random=18', 25, 4.4, 45, 'grooming,kit,complete', 0),
('Scratching Post', 'Multi-level scratching post with hanging toys', 39.99, 49.99, 'accessories', 'https://picsum.photos/300/300?random=19', 15, 4.9, 112, 'furniture,scratching,multi-level', 1),
('Ceramic Food Bowl Set', '2 ceramic bowls with non-slip base', 22.99, 27.99, 'accessories', 'https://picsum.photos/300/300?random=20', 50, 4.7, 89, 'bowls,ceramic,set', 1),
('Window Perch', 'Suction cup window bed for bird watching', 32.99, 39.99, 'accessories', 'https://picsum.photos/300/300?random=21', 30, 4.8, 67, 'perch,window,suction', 1),
('Smart Water Fountain', 'Electric fountain with filter and LED light', 54.99, 64.99, 'accessories', 'https://picsum.photos/300/300?random=22', 18, 4.9, 134, 'fountain,smart,filter', 1),
('Cat Tree Condo', '5-level cat tree with condo and hammock', 89.99, 109.99, 'accessories', 'https://picsum.photos/300/300?random=23', 12, 4.9, 56, 'tree,condo,hammock', 1),
('Heated Cat Bed', 'Electric heated bed for cold nights', 42.99, 49.99, 'accessories', 'https://picsum.photos/300/300?random=24', 22, 4.8, 78, 'heated,winter,comfort', 1),

-- Health Category (8 products)
('Vitamin Supplements', 'Daily vitamins for immune system support', 18.99, 22.99, 'health', 'https://picsum.photos/300/300?random=25', 60, 4.8, 78, 'health,supplements,vitamins', 1),
('Flea & Tick Treatment', 'Monthly topical treatment for cats', 24.99, 29.99, 'health', 'https://picsum.photos/300/300?random=26', 45, 4.7, 112, 'flea,tick,treatment', 1),
('Dental Water Additive', 'Fresh breath and plaque control', 14.99, 19.99, 'health', 'https://picsum.photos/300/300?random=27', 80, 4.5, 156, 'dental,water-additive,fresh-breath', 0),
('Joint Support Chews', 'Glucosamine chews for joint health', 26.99, 31.99, 'health', 'https://picsum.photos/300/300?random=28', 35, 4.8, 67, 'joint,support,chews', 1),
('Probiotic Powder', 'Digestive health support powder', 19.99, 24.99, 'health', 'https://picsum.photos/300/300?random=29', 55, 4.6, 89, 'probiotic,digestive,powder', 0),
('First Aid Kit', 'Complete pet first aid kit', 34.99, 39.99, 'health', 'https://picsum.photos/300/300?random=30', 25, 4.9, 45, 'first-aid,emergency,kit', 1),
('Omega-3 Fish Oil', 'Skin and coat supplement', 22.99, 27.99, 'health', 'https://picsum.photos/300/300?random=31', 40, 4.7, 92, 'omega-3,fish-oil,coat', 1),
('Calming Diffuser', 'Pheromone diffuser for stress relief', 32.99, 39.99, 'health', 'https://picsum.photos/300/300?random=32', 30, 4.6, 78, 'calming,diffuser,stress', 1),

-- Litter Category (8 products)
('Clumping Cat Litter', '99% dust-free clumping litter', 24.99, 29.99, 'litter', 'https://picsum.photos/300/300?random=33', 70, 4.7, 189, 'clumping,dust-free,litter', 1),
('Self-Cleaning Litter Box', 'Automatic cleaning litter box', 199.99, 249.99, 'litter', 'https://picsum.photos/300/300?random=34', 8, 4.8, 45, 'self-cleaning,automatic,smart', 1),
('Litter Deodorizer', 'Natural odor eliminating powder', 12.99, 16.99, 'litter', 'https://picsum.photos/300/300?random=35', 90, 4.4, 112, 'deodorizer,odor-control', 0),
('Large Litter Box', 'Extra large covered litter box', 34.99, 42.99, 'litter', 'https://picsum.photos/300/300?random=36', 40, 4.6, 78, 'large,covered,box', 1),
('Biodegradable Litter', 'Eco-friendly wheat-based litter', 29.99, 34.99, 'litter', 'https://picsum.photos/300/300?random=37', 35, 4.8, 67, 'biodegradable,eco-friendly,wheat', 1),
('Litter Mat', 'Trapping mat to contain litter scatter', 18.99, 24.99, 'litter', 'https://picsum.photos/300/300?random=38', 60, 4.5, 134, 'mat,scatter-control', 0),
('Scented Litter Crystals', 'Long-lasting odor control crystals', 16.99, 21.99, 'litter', 'https://picsum.photos/300/300?random=39', 75, 4.3, 89, 'scented,crystals,odor-control', 0),
('Litter Scoop Set', 'Heavy-duty scoops with holder', 14.99, 19.99, 'litter', 'https://picsum.photos/300/300?random=40', 85, 4.7, 156, 'scoop,set,heavy-duty', 1),

-- Travel Category (8 products)
('Soft-Sided Carrier', 'Lightweight airline-approved carrier', 45.99, 54.99, 'travel', 'https://picsum.photos/300/300?random=41', 25, 4.8, 89, 'carrier,soft-sided,airline', 1),
('Car Seat Cover', 'Waterproof backseat protector', 39.99, 47.99, 'travel', 'https://picsum.photos/300/300?random=42', 30, 4.6, 67, 'car-seat,cover,waterproof', 0),
('Cat Backpack', 'Bubble window backpack carrier', 59.99, 69.99, 'travel', 'https://picsum.photos/300/300?random=43', 18, 4.9, 45, 'backpack,bubble-window', 1),
('Travel Water Bottle', 'Portable water bottle with bowl', 12.99, 16.99, 'travel', 'https://picsum.photos/300/300?random=44', 65, 4.5, 112, 'water,bottle,travel', 0),
('Hard-Sided Carrier', 'Ventilated hard carrier for safety', 52.99, 62.99, 'travel', 'https://picsum.photos/300/300?random=45', 22, 4.7, 78, 'hard-sided,ventilated,safe', 1),
('Car Harness', 'Adjustable car safety harness', 28.99, 34.99, 'travel', 'https://picsum.photos/300/300?random=46', 40, 4.6, 56, 'harness,car-safety', 1),
('Foldable Travel Bowl', 'Silicone collapsible food bowl', 9.99, 14.99, 'travel', 'https://picsum.photos/300/300?random=47', 100, 4.4, 189, 'bowl,collapsible,silicone', 0),
('Travel Litter Box', 'Portable disposable litter box', 15.99, 19.99, 'travel', 'https://picsum.photos/300/300?random=48', 55, 4.7, 92, 'litter-box,portable,disposable', 1);

-- Create admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, is_admin) VALUES 
('admin', 'admin@takecareofkitten.lb', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 1);

-- Insert sample subscriber
INSERT INTO subscribers (email) VALUES ('test@example.com');

-- Insert sample order for testing tracking (must come after products are inserted)
INSERT INTO orders (order_id, customer_name, customer_email, customer_phone, shipping_address, city, subtotal, shipping_fee, total, status, payment_method, payment_status) VALUES
('ORD202312150001', 'John Doe', 'john@example.com', '+96170123456', '123 Main Street, Hamra', 'Beirut', 89.97, 5.00, 94.97, 'processing', 'cash', 'pending');

-- Insert sample order items (must come after orders and products are inserted)
INSERT INTO order_items (order_id, product_id, product_name, price, quantity, image) VALUES
(1, 1, 'Premium Dry Cat Food', 25.99, 2, 'https://picsum.photos/300/300?random=1'),
(1, 9, 'Interactive Laser Toy', 19.99, 1, 'https://picsum.photos/300/300?random=9'),
(1, 17, 'Luxury Cat Bed', 45.99, 1, 'https://picsum.photos/300/300?random=17');

-- ============================================
-- CREATE INDEXES FOR PERFORMANCE
-- ============================================

-- Products indexes
CREATE INDEX idx_products_category ON products(category);
CREATE INDEX idx_products_price ON products(price);
CREATE INDEX idx_products_stock ON products(stock);
CREATE INDEX idx_products_featured ON products(featured);
CREATE INDEX idx_products_rating ON products(rating);

-- Orders indexes
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_customer_email ON orders(customer_email);
CREATE INDEX idx_orders_created_at ON orders(created_at);

-- Order items indexes
CREATE INDEX idx_order_items_order_id ON order_items(order_id);
CREATE INDEX idx_order_items_product_id ON order_items(product_id);

-- Users indexes
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_is_admin ON users(is_admin);

-- Cart indexes
CREATE INDEX idx_cart_session_id ON cart(session_id);
CREATE INDEX idx_cart_user_id ON cart(user_id);

-- Subscribers indexes
CREATE INDEX idx_subscribers_email ON subscribers(email);

-- Contact messages indexes
CREATE INDEX idx_contact_messages_status ON contact_messages(status);
CREATE INDEX idx_contact_messages_created_at ON contact_messages(created_at);

-- ============================================
-- COMPLETION MESSAGE
-- ============================================

SELECT 'Database setup completed successfully!' AS message;
SELECT COUNT(*) AS total_products FROM products;
SELECT username, email, is_admin FROM users;
SELECT 'Admin login: admin / admin123' AS credentials;