<?php
// This is a reusable sidebar component for admin pages
?>
<!-- Sidebar -->
<div class="w-64 bg-gray-900 text-white">
    <div class="p-6">
        <h2 class="text-2xl font-bold">Admin Panel</h2>
        <p class="text-gray-400 text-sm">Take Care of Kitten</p>
    </div>
    <nav class="mt-6">
        <a href="admin.php" class="block py-3 px-6 <?php echo $current_page == 'admin' ? 'bg-gray-800' : 'hover:bg-gray-800'; ?>">
            <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
        </a>
        <a href="admin-orders.php" class="block py-3 px-6 <?php echo $current_page == 'admin-orders' ? 'bg-gray-800' : 'hover:bg-gray-800'; ?>">
            <i class="fas fa-shopping-bag mr-3"></i>Orders
        </a>
        <a href="admin-products.php" class="block py-3 px-6 <?php echo $current_page == 'admin-products' ? 'bg-gray-800' : 'hover:bg-gray-800'; ?>">
            <i class="fas fa-box mr-3"></i>Products
        </a>
        <a href="admin-customers.php" class="block py-3 px-6 <?php echo $current_page == 'admin-customers' ? 'bg-gray-800' : 'hover:bg-gray-800'; ?>">
            <i class="fas fa-users mr-3"></i>Customers
        </a>
        <a href="admin-analytics.php" class="block py-3 px-6 <?php echo $current_page == 'admin-analytics' ? 'bg-gray-800' : 'hover:bg-gray-800'; ?>">
            <i class="fas fa-chart-bar mr-3"></i>Analytics
        </a>
        <div class="mt-12 pt-6 border-t border-gray-800">
            <a href="index.php" class="block py-3 px-6 hover:bg-gray-800">
                <i class="fas fa-store mr-3"></i>Back to Store
            </a>
            <a href="logout.php" class="block py-3 px-6 hover:bg-gray-800">
                <i class="fas fa-sign-out-alt mr-3"></i>Logout
            </a>
        </div>
    </nav>
</div>