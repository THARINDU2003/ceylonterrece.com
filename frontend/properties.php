<?php
session_start();
require_once 'db.php';

// Build dynamic query for properties page
$sql = "SELECT * FROM properties WHERE status = 'published'";
$params = [];

// Apply filters if passed via GET
if (!empty($_GET['type'])) {
    $sql .= " AND property_type = ?";
    $params[] = $_GET['type'];
}
if (!empty($_GET['offer'])) {
    $sql .= " AND offer_type = ?";
    $params[] = $_GET['offer'];
}
if (!empty($_GET['minPrice'])) {
    $sql .= " AND price >= ?";
    $params[] = $_GET['minPrice'];
}
if (!empty($_GET['maxPrice'])) {
    $sql .= " AND price <= ?";
    $params[] = $_GET['maxPrice'];
}
if (!empty($_GET['bedrooms'])) {
    $sql .= " AND bedrooms >= ?";
    $params[] = $_GET['bedrooms'];
}

$sql .= " ORDER BY created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties - CeylonTerrece.com</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
</head>

<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <a href="index.php" class="logo-container flex items-center gap-2">
                    <video src="/images/Logo_name_animated_202604111211.mp4" autoplay loop muted playsinline class="h-16 rounded-lg pointer-events-none"></video>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center space-x-6">
                    <a href="index.php" class="text-gray-700 hover:text-blue-600 font-medium">Home</a>
                    <a href="properties.php" class="text-blue-600 font-bold border-b-2 border-blue-600">Properties</a>
                    <a href="construction.php" class="text-gray-700 hover:text-blue-600 font-medium">Construction</a>
                    <a href="pricing.php" class="text-gray-700 hover:text-blue-600 font-medium">Ad Plans</a>
                    
                    <!-- Auth Section -->
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <div id="navGuestLinks" class="flex items-center space-x-4 border-l pl-4 border-gray-200">
                            <a href="login.php" class="text-gray-700 hover:text-blue-600 font-medium">Login</a>
                            <a href="signup.php" class="bg-blue-50 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-100 font-bold transition">Sign Up</a>
                        </div>
                    <?php else: ?>
                        <div id="navUserLinks" class="flex items-center space-x-4 border-l pl-4 border-gray-200">
                            <a href="dashboard.php" class="text-gray-700 hover:text-blue-600 font-medium flex items-center gap-2">
                                <i class="fas fa-user-circle text-xl"></i>
                                <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            </a>
                            <a href="logout.php" class="text-gray-400 hover:text-red-500 transition" title="Logout">
                                <i class="fas fa-sign-out-alt"></i>
                            </a>
                        </div>
                    <?php endif; ?>

                    <a href="dashboard.php?action=post"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 shadow-md transition transform active:scale-95">Post Free Ad</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Filters Sidebar -->
            <div class="w-full md:w-1/4 bg-white p-6 rounded-2xl shadow-sm h-fit">
                <h3 class="font-bold text-xl mb-6 text-gray-800">Search Filters</h3>
                <form action="properties.php" method="GET" class="space-y-6">
                    <div>
                        <label for="type" class="block text-sm font-bold text-gray-700 mb-2">Property Type</label>
                        <select name="type" id="type" class="w-full border-2 border-gray-100 rounded-xl p-3 focus:border-blue-600 outline-none transition">
                            <option value="">All Types</option>
                            <option value="Land" <?php echo (isset($_GET['type']) && $_GET['type'] == 'Land') ? 'selected' : ''; ?>>Land</option>
                            <option value="House" <?php echo (isset($_GET['type']) && $_GET['type'] == 'House') ? 'selected' : ''; ?>>House</option>
                            <option value="Apartment" <?php echo (isset($_GET['type']) && $_GET['type'] == 'Apartment') ? 'selected' : ''; ?>>Apartment</option>
                        </select>
                    </div>
                    <div>
                        <label for="offer" class="block text-sm font-bold text-gray-700 mb-2">Offer Type</label>
                        <select name="offer" id="offer" class="w-full border-2 border-gray-100 rounded-xl p-3 focus:border-blue-600 outline-none transition">
                            <option value="">All Offers</option>
                            <option value="Sale" <?php echo (isset($_GET['offer']) && $_GET['offer'] == 'Sale') ? 'selected' : ''; ?>>For Sale</option>
                            <option value="Rent" <?php echo (isset($_GET['offer']) && $_GET['offer'] == 'Rent') ? 'selected' : ''; ?>>For Rent</option>
                        </select>
                    </div>
                    <div>
                        <label for="minPrice" class="block text-sm font-bold text-gray-700 mb-2">Min Price (LKR)</label>
                        <input type="number" name="minPrice" id="minPrice" placeholder="Min" value="<?php echo htmlspecialchars($_GET['minPrice'] ?? ''); ?>" class="w-full border-2 border-gray-100 rounded-xl p-3">
                    </div>
                    <div>
                        <label for="maxPrice" class="block text-sm font-bold text-gray-700 mb-2">Max Price (LKR)</label>
                        <input type="number" name="maxPrice" id="maxPrice" placeholder="Max" value="<?php echo htmlspecialchars($_GET['maxPrice'] ?? ''); ?>" class="w-full border-2 border-gray-100 rounded-xl p-3">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold hover:bg-blue-700 shadow-lg shadow-blue-100 transition">
                        Apply Filters
                    </button>
                    <a href="properties.php" class="block text-center w-full text-gray-500 font-medium py-2 hover:text-blue-600">
                        Reset All
                    </a>
                </form>
            </div>

            <!-- Properties List -->
            <div class="w-full md:w-3/4">
                <div class="flex justify-between items-center mb-6 bg-white p-4 rounded-xl shadow-sm">
                    <h2 class="text-xl font-bold text-gray-800"><span id="propertyCount"><?php echo count($properties); ?></span> Properties</h2>
                </div>
                
                <div id="propertiesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if (empty($properties)): ?>
                        <div class="col-span-full py-20 text-center">
                            <p class="text-gray-500">No properties found matching your criteria.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($properties as $prop): ?>
                            <?php 
                                $images = json_decode($prop['images'], true);
                                $bgImage = (!empty($images) && isset($images[0])) ? htmlspecialchars($images[0]) : 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';
                            ?>
                            <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                                <div class="h-48 bg-cover bg-center" style="background-image: url('<?php echo $bgImage; ?>');"></div>
                                <div class="p-4">
                                    <h3 class="font-bold text-lg mb-1 truncate"><?php echo htmlspecialchars($prop['title']); ?></h3>
                                    <p class="text-gray-500 text-sm mb-3"><i class="fas fa-map-marker-alt mr-1"></i> <?php echo htmlspecialchars($prop['city']); ?></p>
                                    <div class="flex justify-between items-center mt-4">
                                        <span class="font-bold text-blue-600 text-lg">Rs. <?php echo number_format($prop['price']); ?></span>
                                        <a href="property-detail.php?id=<?php echo $prop['id']; ?>" class="bg-gray-100 text-gray-700 px-3 py-1 rounded-lg hover:bg-gray-200 text-sm font-medium">View Details</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
