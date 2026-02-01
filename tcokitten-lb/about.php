<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$current_page = 'about';
?>
<?php include 'includes/header.php'; ?>

<main>
    <!-- About Header -->
    <section class="bg-gradient-to-r from-primary to-indigo-600 text-white py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl font-bold mb-4">About Us</h1>
            <p class="text-xl opacity-90">Our story, mission, and commitment to feline care</p>
        </div>
    </section>

    <!-- Our Story -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="animate-fade-in">
                    <h2 class="text-3xl font-bold text-dark mb-6">Our Story</h2>
                    <p class="text-gray-600 mb-6">
                        Founded in 2020, Take Care of Kitten Lebanon was born out of a passion for feline well-being. 
                        Our founder, Sarah Johnson, noticed a gap in the Lebanese market for premium, vet-approved cat products 
                        that prioritize both quality and safety.
                    </p>
                    <p class="text-gray-600 mb-6">
                        What started as a small online store has grown into Lebanon's leading destination for cat care products. 
                        We've built our reputation on trust, quality, and exceptional customer service.
                    </p>
                    <p class="text-gray-600">
                        Today, we serve thousands of cat owners across Lebanon, helping them provide the best possible care 
                        for their feline companions.
                    </p>
                </div>
                <div class="animate-fade-in">
                    <img src="https://picsum.photos/600/400?random=10" alt="Our team with cats" class="rounded-2xl shadow-xl">
                </div>
            </div>
        </div>
    </section>

    <!-- Our Mission -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-dark mb-4">Our Mission & Values</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">We're committed to enhancing the lives of cats and their owners through premium products and expert guidance.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-xl p-8 shadow-lg">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-heart text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-center text-dark mb-4">Passion for Cats</h3>
                    <p class="text-gray-600 text-center">Every product we offer is carefully selected with your cat's health and happiness in mind.</p>
                </div>
                
                <div class="bg-white rounded-xl p-8 shadow-lg">
                    <div class="w-16 h-16 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-award text-accent text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-center text-dark mb-4">Quality First</h3>
                    <p class="text-gray-600 text-center">We only stock products that meet our strict quality standards and are vet-approved.</p>
                </div>
                
                <div class="bg-white rounded-xl p-8 shadow-lg">
                    <div class="w-16 h-16 bg-secondary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-users text-secondary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-center text-dark mb-4">Community Focus</h3>
                    <p class="text-gray-600 text-center">We're building a community of cat lovers who share knowledge, experiences, and support.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Team -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-dark mb-12">Meet Our Team</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-40 h-40 mx-auto mb-6 rounded-full overflow-hidden border-4 border-primary/20">
                        <img src="https://picsum.photos/200/200?random=11" alt="Sarah Johnson" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-semibold text-dark mb-2">Sarah Johnson</h3>
                    <p class="text-primary font-medium mb-2">Founder & CEO</p>
                    <p class="text-gray-600 text-sm">Certified feline nutritionist with 15+ years of experience</p>
                </div>
                
                <div class="text-center">
                    <div class="w-40 h-40 mx-auto mb-6 rounded-full overflow-hidden border-4 border-primary/20">
                        <img src="https://picsum.photos/200/200?random=12" alt="Dr. Michael Chen" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-semibold text-dark mb-2">Dr. Michael Chen</h3>
                    <p class="text-primary font-medium mb-2">Veterinary Advisor</p>
                    <p class="text-gray-600 text-sm">DVM with specialization in feline medicine</p>
                </div>
                
                <div class="text-center">
                    <div class="w-40 h-40 mx-auto mb-6 rounded-full overflow-hidden border-4 border-primary/20">
                        <img src="https://picsum.photos/200/200?random=13" alt="Layla Hassan" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-semibold text-dark mb-2">Layla Hassan</h3>
                    <p class="text-primary font-medium mb-2">Product Manager</p>
                    <p class="text-gray-600 text-sm">Expert in sourcing premium cat products</p>
                </div>
                
                <div class="text-center">
                    <div class="w-40 h-40 mx-auto mb-6 rounded-full overflow-hidden border-4 border-primary/20">
                        <img src="https://picsum.photos/200/200?random=14" alt="Alexei Petrov" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-semibold text-dark mb-2">Alexei Petrov</h3>
                    <p class="text-primary font-medium mb-2">Customer Support Lead</p>
                    <p class="text-gray-600 text-sm">Available 24/7 to assist with your questions</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Achievements -->
    <section class="py-16 bg-gradient-to-r from-primary to-indigo-600 text-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-5xl font-bold mb-2">5,000+</div>
                    <p class="text-xl">Happy Customers</p>
                </div>
                
                <div class="text-center">
                    <div class="text-5xl font-bold mb-2">500+</div>
                    <p class="text-xl">Products Available</p>
                </div>
                
                <div class="text-center">
                    <div class="text-5xl font-bold mb-2">24/7</div>
                    <p class="text-xl">Support Available</p>
                </div>
                
                <div class="text-center">
                    <div class="text-5xl font-bold mb-2">100%</div>
                    <p class="text-xl">Satisfaction Guarantee</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-dark mb-6">Join Our Cat-Loving Community</h2>
            <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">Sign up for our newsletter to receive expert tips, new product alerts, and exclusive offers.</p>
            
            <form action="subscribe.php" method="POST" class="max-w-md mx-auto">
                <div class="flex flex-col sm:flex-row gap-4">
                    <input type="email" name="email" placeholder="Your email address" required
                           class="flex-1 px-6 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    <button type="submit" class="bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors">
                        Subscribe Now
                    </button>
                </div>
            </form>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>