<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$current_page = 'contact';

// Handle form submission
$message_sent = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);
    
    // Validate input
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Send email (in production, this would connect to a real email service)
        $to = ADMIN_EMAIL;
        $email_subject = "Contact Form: $subject";
        $email_body = "Name: $name\n";
        $email_body .= "Email: $email\n";
        $email_body .= "Phone: $phone\n\n";
        $email_body .= "Message:\n$message\n";
        
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        
        // In production, use a proper email sending method
        // For demo purposes, we'll just set a success message
        $message_sent = true;
        
        // In production, uncomment this:
        // $message_sent = mail($to, $email_subject, $email_body, $headers);
    }
}
?>
<?php include 'includes/header.php'; ?>

<main>
    <!-- Contact Header -->
    <section class="bg-gradient-to-r from-primary to-indigo-600 text-white py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl font-bold mb-4">Contact Us</h1>
            <p class="text-xl opacity-90">We're here to help with all your cat care questions</p>
        </div>
    </section>

    <!-- Contact Form & Info -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div>
                    <h2 class="text-3xl font-bold text-dark mb-8">Send Us a Message</h2>
                    
                    <?php if ($message_sent): ?>
                        <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-6">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-check text-green-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-semibold text-green-800">Message Sent Successfully!</h3>
                                    <p class="text-green-600">We'll get back to you within 24 hours.</p>
                                </div>
                            </div>
                            <a href="contact.php" class="inline-block bg-green-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700">
                                Send Another Message
                            </a>
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
                    
                    <form action="contact.php" method="POST" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 mb-2">Full Name *</label>
                                <input type="text" name="name" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 mb-2">Email Address *</label>
                                <input type="email" name="email" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" name="phone"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                   value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Subject *</label>
                            <select name="subject" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">Select a subject</option>
                                <option value="Product Inquiry" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Product Inquiry') ? 'selected' : ''; ?>>Product Inquiry</option>
                                <option value="Order Support" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Order Support') ? 'selected' : ''; ?>>Order Support</option>
                                <option value="Shipping Questions" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Shipping Questions') ? 'selected' : ''; ?>>Shipping Questions</option>
                                <option value="Returns & Refunds" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Returns & Refunds') ? 'selected' : ''; ?>>Returns & Refunds</option>
                                <option value="Cat Care Advice" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Cat Care Advice') ? 'selected' : ''; ?>>Cat Care Advice</option>
                                <option value="Other" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Message *</label>
                            <textarea name="message" required rows="6"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        </div>
                        
                        <button type="submit" class="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors">
                            Send Message <i class="fas fa-paper-plane ml-2"></i>
                        </button>
                    </form>
                </div>

                <!-- Contact Information -->
                <div>
                    <div class="bg-gray-50 rounded-2xl p-8">
                        <h3 class="text-2xl font-bold text-dark mb-8">Get in Touch</h3>
                        
                        <div class="space-y-8">
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-map-marker-alt text-primary text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-dark mb-2">Our Location</h4>
                                    <p class="text-gray-600">Beirut Central District</p>
                                    <p class="text-gray-600">Beirut, Lebanon</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-phone text-primary text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-dark mb-2">Phone Number</h4>
                                    <p class="text-gray-600">+961 1 234 567</p>
                                    <p class="text-gray-600">Mon-Sat: 9AM - 8PM</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-envelope text-primary text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-dark mb-2">Email Address</h4>
                                    <p class="text-gray-600">info@takecareofkitten.lb</p>
                                    <p class="text-gray-600">support@takecareofkitten.lb</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Business Hours -->
                        <div class="mt-12">
                            <h4 class="text-xl font-semibold text-dark mb-6">Business Hours</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center py-3 border-b">
                                    <span class="text-gray-700">Monday - Friday</span>
                                    <span class="font-semibold">9:00 AM - 8:00 PM</span>
                                </div>
                                <div class="flex justify-between items-center py-3 border-b">
                                    <span class="text-gray-700">Saturday</span>
                                    <span class="font-semibold">9:00 AM - 6:00 PM</span>
                                </div>
                                <div class="flex justify-between items-center py-3 border-b">
                                    <span class="text-gray-700">Sunday</span>
                                    <span class="font-semibold">10:00 AM - 4:00 PM</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Emergency Contact -->
                        <div class="mt-12 p-6 bg-red-50 rounded-xl">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-exclamation-triangle text-red-500"></i>
                                </div>
                                <h4 class="text-lg font-semibold text-red-800">Emergency Veterinary Help</h4>
                            </div>
                            <p class="text-red-700 mb-3">For urgent veterinary assistance outside business hours:</p>
                            <p class="text-red-800 font-semibold">+961 70 123 456</p>
                            <p class="text-red-600 text-sm mt-2">Available 24/7 for emergencies</p>
                        </div>
                    </div>
                    
                    <!-- Social Media -->
                    <div class="mt-8">
                        <h4 class="text-xl font-semibold text-dark mb-6">Follow Us</h4>
                        <div class="flex space-x-4">
                            <a href="#" class="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary transition-colors">
                                <i class="fab fa-facebook-f text-white"></i>
                            </a>
                            <a href="#" class="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary transition-colors">
                                <i class="fab fa-instagram text-white"></i>
                            </a>
                            <a href="#" class="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary transition-colors">
                                <i class="fab fa-twitter text-white"></i>
                            </a>
                            <a href="#" class="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary transition-colors">
                                <i class="fab fa-youtube text-white"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-dark mb-12">Find Us</h2>
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Map Placeholder -->
                <div class="h-96 bg-gray-200 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-map-marked-alt text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">Interactive map would be displayed here</p>
                        <p class="text-gray-500 text-sm mt-2">Location: Beirut Central District, Lebanon</p>
                    </div>
                </div>
                <!-- Map Coordinates -->
                <div class="p-6 border-t">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <h4 class="font-semibold text-gray-700 mb-2">Coordinates</h4>
                            <p class="text-gray-600">33.8938° N, 35.5018° E</p>
                        </div>
                        <div class="text-center">
                            <h4 class="font-semibold text-gray-700 mb-2">Parking</h4>
                            <p class="text-gray-600">Available nearby</p>
                        </div>
                        <div class="text-center">
                            <h4 class="font-semibold text-gray-700 mb-2">Accessibility</h4>
                            <p class="text-gray-600">Wheelchair accessible</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-dark mb-12">Frequently Asked Questions</h2>
            
            <div class="max-w-3xl mx-auto space-y-6">
                <div class="border border-gray-200 rounded-xl p-6">
                    <h4 class="text-xl font-semibold text-dark mb-3">What are your shipping rates and delivery times?</h4>
                    <p class="text-gray-600">We offer free shipping on orders over $50 within Beirut. Standard delivery takes 2-3 business days, while express delivery (1 business day) is available for an additional $5.</p>
                </div>
                
                <div class="border border-gray-200 rounded-xl p-6">
                    <h4 class="text-xl font-semibold text-dark mb-3">Do you offer returns or exchanges?</h4>
                    <p class="text-gray-600">Yes! We offer a 30-day return policy for unopened and unused items. Simply contact our support team to initiate a return.</p>
                </div>
                
                <div class="border border-gray-200 rounded-xl p-6">
                    <h4 class="text-xl font-semibold text-dark mb-3">Are your products vet-approved?</h4>
                    <p class="text-gray-600">Absolutely! All our products are carefully selected and approved by our team of veterinary experts to ensure they meet the highest safety and quality standards.</p>
                </div>
                
                <div class="border border-gray-200 rounded-xl p-6">
                    <h4 class="text-xl font-semibold text-dark mb-3">Can I get advice on which products are best for my cat?</h4>
                    <p class="text-gray-600">Yes! Our team of cat care experts is available 24/7 to provide personalized recommendations. Just contact us through phone, email, or live chat.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>