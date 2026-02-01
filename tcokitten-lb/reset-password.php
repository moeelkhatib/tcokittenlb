<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$current_page = 'login';
$token = $_GET['token'] ?? '';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $token = $_POST['token'];
    
    if (empty($password) || empty($confirm_password)) {
        $error = 'Please enter both password fields.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } else {
        $pdo = getDBConnection();
        
        // Check if token is valid
        $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
        $stmt->execute([$token]);
        $reset = $stmt->fetch();
        
        if ($reset) {
            // Update password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->execute([$hashed_password, $reset['email']]);
            
            // Delete used token
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->execute([$token]);
            
            $message = 'Password reset successfully! You can now login with your new password.';
        } else {
            $error = 'Invalid or expired reset token.';
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<main>
    <!-- Reset Password Header -->
    <section class="bg-gradient-to-r from-primary to-indigo-600 text-white py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl font-bold mb-4">Set New Password</h1>
            <p class="text-xl opacity-90">Create a new password for your account</p>
        </div>
    </section>

    <!-- Reset Password Form -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-md mx-auto">
                <div class="bg-gray-50 rounded-2xl p-8">
                    <?php if ($message): ?>
                        <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                <div>
                                    <h3 class="font-semibold text-green-800 mb-2">Password Reset Successful!</h3>
                                    <p class="text-green-600"><?php echo $message; ?></p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="login.php" class="inline-block bg-green-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700">
                                    Go to Login
                                </a>
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
                    
                    <?php if (!$message): ?>
                        <h2 class="text-2xl font-bold text-dark mb-6">Create New Password</h2>
                        
                        <form action="reset-password.php" method="POST" class="space-y-6">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                            
                            <div>
                                <label class="block text-gray-700 mb-2">New Password *</label>
                                <input type="password" name="password" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                <p class="text-gray-500 text-sm mt-1">Minimum 6 characters</p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 mb-2">Confirm New Password *</label>
                                <input type="password" name="confirm_password" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                            
                            <button type="submit" class="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors">
                                Reset Password <i class="fas fa-lock ml-2"></i>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>