<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: account.php');
    exit;
}

$current_page = 'login';
$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    // Validate input
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['is_admin'] = (bool)$user['is_admin'];
            
            // Redirect based on user type
            if ($user['is_admin']) {
                header('Location: admin.php');
            } else {
                header('Location: account.php');
            }
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = sanitize($_POST['reg_name']);
    $email = sanitize($_POST['reg_email']);
    $password = $_POST['reg_password'];
    $confirm_password = $_POST['reg_confirm_password'];
    
    // Validate registration
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required for registration.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } else {
        // Check if email already exists
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = 'Email already registered. Please login instead.';
        } else {
            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, created_at) VALUES (?, ?, ?, NOW())");
            
            if ($stmt->execute([$name, $email, $hashed_password])) {
                $success = 'Registration successful! Please login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<main>
    <!-- Login Header -->
    <section class="bg-gradient-to-r from-primary to-indigo-600 text-white py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl font-bold mb-4">Account Login</h1>
            <p class="text-xl opacity-90">Access your account or create a new one</p>
        </div>
    </section>

    <!-- Login & Register -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Login Form -->
                <div class="bg-gray-50 rounded-2xl p-8">
                    <h2 class="text-2xl font-bold text-dark mb-6">Login to Your Account</h2>
                    
                    <?php if ($error && !isset($_POST['register'])): ?>
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                                <p class="text-red-700"><?php echo $error; ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <form action="login.php" method="POST" class="space-y-6">
                        <div>
                            <label class="block text-gray-700 mb-2">Email Address *</label>
                            <input type="email" name="email" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Password *</label>
                            <input type="password" name="password" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox" name="remember" class="mr-2">
                                <span class="text-gray-700">Remember me</span>
                            </label>
                            <a href="forgot-password.php" class="text-primary hover:text-indigo-700">
                                Forgot Password?
                            </a>
                        </div>
                        
                        <button type="submit" class="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors">
                            Sign In <i class="fas fa-sign-in-alt ml-2"></i>
                        </button>
                        
                        <div class="text-center">
                            <p class="text-gray-600">Don't have an account? <a href="#register" class="text-primary font-semibold hover:text-indigo-700">Sign up here</a></p>
                        </div>
                    </form>
                </div>

                <!-- Register Form -->
                <div class="bg-gray-50 rounded-2xl p-8" id="register">
                    <h2 class="text-2xl font-bold text-dark mb-6">Create New Account</h2>
                    
                    <?php if ($success): ?>
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                <p class="text-green-700"><?php echo $success; ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error && isset($_POST['register'])): ?>
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                                <p class="text-red-700"><?php echo $error; ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <form action="login.php" method="POST" class="space-y-6">
                        <input type="hidden" name="register" value="1">
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Full Name *</label>
                            <input type="text" name="reg_name" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                   value="<?php echo isset($_POST['reg_name']) ? htmlspecialchars($_POST['reg_name']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Email Address *</label>
                            <input type="email" name="reg_email" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                   value="<?php echo isset($_POST['reg_email']) ? htmlspecialchars($_POST['reg_email']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Password *</label>
                            <input type="password" name="reg_password" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            <p class="text-gray-500 text-sm mt-1">Minimum 6 characters</p>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Confirm Password *</label>
                            <input type="password" name="reg_confirm_password" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" name="terms" required class="mr-2">
                            <span class="text-gray-700 text-sm">
                                I agree to the <a href="terms.php" class="text-primary hover:underline">Terms of Service</a> and <a href="privacy.php" class="text-primary hover:underline">Privacy Policy</a>
                            </span>
                        </div>
                        
                        <button type="submit" class="w-full bg-accent text-white py-3 rounded-lg font-semibold hover:bg-emerald-600 transition-colors">
                            Create Account <i class="fas fa-user-plus ml-2"></i>
                        </button>
                        
                        <div class="text-center">
                            <p class="text-gray-600">Already have an account? <a href="#login" class="text-primary font-semibold hover:text-indigo-700">Sign in here</a></p>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Benefits -->
            <div class="mt-16 text-center">
                <h3 class="text-2xl font-bold text-dark mb-8">Benefits of Having an Account</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="p-6">
                        <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shipping-fast text-primary text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-dark mb-2">Faster Checkout</h4>
                        <p class="text-gray-600">Save your shipping details for quicker purchases</p>
                    </div>
                    <div class="p-6">
                        <div class="w-16 h-16 bg-secondary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-history text-secondary text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-dark mb-2">Order History</h4>
                        <p class="text-gray-600">Track all your orders in one place</p>
                    </div>
                    <div class="p-6">
                        <div class="w-16 h-16 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-tags text-accent text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-dark mb-2">Exclusive Offers</h4>
                        <p class="text-gray-600">Get special discounts and early access to sales</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>