<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$current_page = 'login';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    
    if (empty($email)) {
        $error = 'Please enter your email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Generate reset token (in production, use a secure method)
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store token in database (you need a password_resets table)
            $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$email, $token, $expires]);
            
            // Send email (in production)
            $reset_link = SITE_URL . "/reset-password.php?token=" . $token;
            
            // For demo purposes, show the link
            $message = "Password reset link: <a href='$reset_link'>$reset_link</a> (This would be sent via email in production)";
        } else {
            $error = 'No account found with that email address.';
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<main>
    <!-- Forgot Password Header -->
    <section class="bg-gradient-to-r from-primary to-indigo-600 text-white py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl font-bold mb-4">Reset Your Password</h1>
            <p class="text-xl opacity-90">Enter your email to receive a reset link</p>
        </div>
    </section>

    <!-- Forgot Password Form -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-md mx-auto">
                <div class="bg-gray-50 rounded-2xl p-8">
                    <?php if ($message): ?>
                        <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                <div>
                                    <h3 class="font-semibold text-green-800 mb-2">Check Your Email</h3>
                                    <p class="text-green-600"><?php echo $message; ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                                <p class="text-red-700"><?php echo $error; ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <h2 class="text-2xl font-bold text-dark mb-6">Forgot Password?</h2>
                    <p class="text-gray-600 mb-6">Enter the email address associated with your account and we'll send you a link to reset your password.</p>
                    
                    <form action="forgot-password.php" method="POST" class="space-y-6">
                        <div>
                            <label class="block text-gray-700 mb-2">Email Address *</label>
                            <input type="email" name="email" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        
                        <button type="submit" class="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors">
                            Send Reset Link <i class="fas fa-paper-plane ml-2"></i>
                        </button>
                        
                        <div class="text-center">
                            <a href="login.php" class="text-primary hover:text-indigo-700">
                                <i class="fas fa-arrow-left mr-2"></i> Back to Login
                            </a>
                        </div>
                    </form>
                    
                    <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                        <h4 class="font-semibold text-blue-800 mb-2">Need Help?</h4>
                        <p class="text-blue-600 text-sm">If you're having trouble resetting your password, contact our support team at <?php echo ADMIN_EMAIL; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>