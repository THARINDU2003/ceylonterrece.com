<?php
session_start();
require_once 'db.php';

// Fetch published properties
try {
    $stmt = $pdo->query("SELECT * FROM properties WHERE status = 'published' ORDER BY created_at DESC LIMIT 6");
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // For debugging. In production, log error and show friendly message.
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CeylonTerrace - Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; }
        body { display: flex; height: 100vh; background-color: #f4f6f8; color: #333; }
        /* Sidebar Styles */
        .sidebar { width: 250px; background-color: #ffffff; border-right: 1px solid #e0e0e0; display: flex; flex-direction: column; overflow-y: auto; }
        .sidebar-logo-container { padding: 20px 15px 10px; }
        .sidebar-logo-container img { max-width: 100%; height: auto; }
        .user-account-info { padding: 0 20px 15px; font-size: 13px; }
        .user-account-info .acc-title { color: #555; margin-bottom: 2px; }
        .user-account-info .acc-name { font-weight: 700; font-size: 15px; color: #000; display: flex; align-items: center; gap: 5px; }
        .sidebar-search { padding: 0 20px 15px; }
        .sidebar-search-wrapper { position: relative; }
        .sidebar-search-wrapper input { width: 100%; padding: 8px 10px 8px 30px; border: 1px solid #ccc; border-radius: 20px; font-size: 13px; outline: none; background: #fdfdfd; }
        .sidebar-search-wrapper i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #888; font-size: 12px; }
        .sidebar-menu { list-style: none; }
        .sidebar-menu li { padding: 10px 20px; font-size: 15px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 8px; color: #111; }
        .sidebar-menu li:hover { background-color: #f0f0f0; }
        .sidebar-menu li a { text-decoration: none; color: inherit; display: flex; align-items: center; gap: 8px; width: 100%; }
        .sidebar-menu li i { font-size: 12px; width: 14px; text-align: center; }
        .sidebar-bottom { margin-top: auto; padding: 20px; display: flex; align-items: center; justify-content: space-between; }
        .sidebar-bottom img { width: 90px; }
        .social-icons { display: flex; flex-direction: column; gap: 10px; font-size: 18px; color: #000; }
        /* Main Content Styles */
        .main-content { flex: 1; display: flex; flex-direction: column; overflow-y: auto; background-color: #ffffff; }
        .top-navbar { display: flex; justify-content: space-between; align-items: center; padding: 20px 30px; border-bottom: 1px solid #e0e0e0; }
        .nav-links { display: flex; gap: 12px; }
        .nav-link { padding: 6px 14px; border-radius: 6px; font-size: 14px; font-weight: 600; text-decoration: none; color: #333; border: 1px solid #ccc; }
        .nav-link.active { background-color: #3f7b53; color: #fff; border-color: #3f7b53; }
        .top-icons { display: flex; gap: 18px; font-size: 20px; color: #333; }
        .top-icons i { cursor: pointer; }
        /* Filter / Search Bar */
        .filter-bar { display: flex; align-items: center; padding: 20px 30px 10px; gap: 15px; }
        .filter-input-wrapper { position: relative; }
        .filter-input-wrapper.search-main { flex: 1; max-width: 300px; }
        .filter-input-wrapper input, .filter-input-wrapper select { width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; outline: none; }
        .filter-input-wrapper.search-main input { padding-left: 32px; border-radius: 20px; }
        .filter-input-wrapper.search-main i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #888; }
        .post-add-btn { background-color: #3f7b53; color: white; border: none; padding: 8px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 14px; text-decoration: none; }
        /* Content Area */
        .dashboard-content { padding: 20px 30px; }
        .section-title { font-size: 20px; font-weight: 700; margin-bottom: 20px; }
        .ads-grid { display: flex; gap: 25px; flex-wrap: wrap; }
        .ad-card { width: 260px; background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); padding: 12px; display: flex; flex-direction: column; text-decoration: none; color: inherit; }
        .ad-card:hover { transform: translateY(-3px); transition: 0.2s; }
        .ad-image { height: 160px; border-radius: 8px; background-size: cover; background-position: center; margin-bottom: 12px; }
        .ad-title { font-size: 15px; font-weight: 700; margin-bottom: 5px; line-height: 1.3; }
        .ad-divider { color: #555; font-size: 16px; margin: 4px 0; }
        .ad-price { font-size: 15px; font-weight: 700; color: #111; }
        .carousel-dots { display: flex; justify-content: center; gap: 8px; margin-top: 30px; }
        .dot { width: 10px; height: 10px; border-radius: 50%; border: 1px solid #777; background: transparent; cursor: pointer; }
        .dot.active { background: #3f7b53; border-color: #3f7b53; }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-logo-container">
            <a href="index.php"><img src="/images/logo.png" alt="CeylonTerrace.com" onerror="this.src='https://cdn-icons-png.flaticon.com/512/8207/8207185.png'; this.style.filter='hue-rotate(80deg) brightness(0.6)'; this.style.width='120px';"></a>
        </div>
        
        <div class="user-account-info">
            <div class="acc-title">User Account</div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="acc-name" id="userAccountName"><?php echo htmlspecialchars($_SESSION['user_name']); ?> &hearts;</div>
            <?php else: ?>
                <div class="acc-name"><a href="login.php" style="text-decoration:none; color:inherit;">Guest - Login</a></div>
            <?php endif; ?>
        </div>

        <div class="sidebar-search">
            <div class="sidebar-search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search">
            </div>
        </div>

        <ul class="sidebar-menu">
            <li><a href="index.php"><i class="fas fa-caret-right"></i> HOME</a></li>
            <li><a href="properties.php"><i class="fas fa-caret-right"></i> Properties</a></li>
            <li><a href="construction.php"><i class="fas fa-caret-right"></i> Construction</a></li>
            <li><a href="pricing.php"><i class="fas fa-caret-right"></i> Ad plans</a></li>
            <li><a href="jobs.php"><i class="fas fa-caret-right"></i> JOBS</a></li>
            <li><a href="#"><i class="fas fa-caret-right"></i> Messages</a></li>
            <li><a href="learn.php"><i class="fas fa-caret-right"></i> Learn</a></li>
            <li><a href="contacts.php"><i class="fas fa-caret-right"></i> Contact</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="logout.php"><i class="fas fa-caret-right"></i> Logout</a></li>
            <?php endif; ?>
        </ul>

        <div class="sidebar-bottom">
            <img src="/images/logo.png" alt="Logo" onerror="this.src='https://cdn-icons-png.flaticon.com/512/8207/8207185.png'; this.style.filter='hue-rotate(80deg) brightness(0.6)';">
            <div class="social-icons">
                <i class="fab fa-youtube"></i>
                <i class="fab fa-facebook-f"></i>
                <i class="fab fa-tiktok"></i>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        
        <!-- Top Nav -->
        <div class="top-navbar">
            <div class="nav-links">
                <a href="index.php" class="nav-link active">HOME</a>
                <a href="properties.php" class="nav-link">Property</a>
                <a href="construction.php" class="nav-link">Construction</a>
                <a href="learn.php" class="nav-link">Learn</a>
                <a href="jobs.php" class="nav-link">JOBS</a>
            </div>
            <div class="top-icons">
                <a href="login.php" style="color:inherit;"><i class="fas fa-user-circle"></i></a>
                <a href="index.php" style="color:inherit;"><i class="fas fa-home"></i></a>
                <i class="fas fa-sliders-h"></i>
                <i class="fas fa-search"></i>
                <i class="far fa-question-circle"></i>
            </div>
        </div>

        <!-- Filter / Search -->
        <div class="filter-bar">
            <div class="filter-input-wrapper search-main">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search">
            </div>
            <div class="filter-input-wrapper">
                <select>
                    <option>New IS P</option>
                    <option>For Sale</option>
                    <option>For Rent</option>
                </select>
            </div>
            <div class="filter-input-wrapper">
                <input type="text" placeholder="Location">
            </div>
            <a href="dashboard.php?action=post" class="post-add-btn">post Add</a>
        </div>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <h2 class="section-title">Top Ads,</h2>
            
            <div class="ads-grid" id="topAdsContainer">
                <?php if (empty($properties)): ?>
                    <p>No properties available currently.</p>
                <?php else: ?>
                    <?php foreach ($properties as $prop): ?>
                        <?php 
                            // Decode JSON images if any
                            $images = json_decode($prop['images'], true);
                            $bgImage = (!empty($images) && isset($images[0])) ? htmlspecialchars($images[0]) : 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';
                        ?>
                        <a href="property-detail.php?id=<?php echo $prop['id']; ?>" class="ad-card">
                            <div class="ad-image" style="background-image: url('<?php echo $bgImage; ?>');"></div>
                            <div class="ad-title"><?php echo htmlspecialchars($prop['title']); ?><br><?php echo htmlspecialchars($prop['city'] ?? ''); ?></div>
                            <div class="ad-divider"><i class="fas fa-bars"></i></div>
                            <div class="ad-price">Rs. <?php echo number_format($prop['price'], 2); ?></div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="carousel-dots">
                <div class="dot active"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
        </div>

    </div>
</body>
</html>
