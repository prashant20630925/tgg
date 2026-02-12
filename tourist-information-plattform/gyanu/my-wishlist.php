<?php

require_once 'auth_check.php';
include "config.php";

$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - Destinations</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/modern.css">
    <link rel="stylesheet" href="./css/theme-color.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        h1 { margin-bottom: 30px; color: #333; }
        .wishlist-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        .wishlist-card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .wishlist-card:hover { transform: translateY(-5px); }
        .card-image { width: 100%; height: 200px; object-fit: cover; }
        .card-content { padding: 15px; }
        .card-title { font-size: 18px; font-weight: 600; margin-bottom: 8px; }
        .card-location { font-size: 13px; color: #999; margin-bottom: 12px; }
        .card-buttons { display: flex; gap: 10px; }
        .btn { padding: 8px 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 13px; text-decoration: none; display: inline-block; text-align: center; }
        .btn-view { background: #1e73be; color: white; }
        .btn-remove { background: #e74c3c; color: white; }
        .btn:hover { opacity: 0.9; }
        .empty { text-align: center; padding: 60px 20px; color: #999; }
        @media (max-width: 768px) {
            .wishlist-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="breadcrumb" style="margin-bottom: 20px; font-size: 14px;">
            <a href="mainpage.php" style="color: #1e73be; text-decoration: none;">Home</a> / My Wishlist
        </div>
        <h1>❤️ My Wishlist</h1>
        <div class="wishlist-grid" id="wishlist-container">
            <p>Loading...</p>
        </div>
    </div>

    <script>
        const user_id = <?php echo $user_id; ?>;

        function loadWishlist() {
            fetch('api-wishlist.php?action=get_all')
                .then(r => r.json())
                .then(data => {
                    const container = document.getElementById('wishlist-container');
                    if (!data.wishlist || data.wishlist.length === 0) {
                        container.innerHTML = '<div class="empty" style="grid-column: 1/-1;"><p>Your wishlist is empty</p><a href="destination.php" style="color: #1e73be;">Browse destinations</a></div>';
                        return;
                    }
                    container.innerHTML = data.wishlist.map(d => `
                        <div class="wishlist-card">
                            <img src="images/destination/${d.image}" alt="${d.name}" class="card-image">
                            <div class="card-content">
                                <div class="card-title">${d.name}</div>
                                <div class="card-location">📍 ${d.location}</div>
                                <div class="card-buttons">
                                    <a href="destination-details-enhanced.php?id=${d.id}" class="btn btn-view">View Details</a>
                                    <button class="btn btn-remove" onclick="removeFromWishlist(${d.id})">Remove</button>
                                </div>
                            </div>
                        </div>
                    `).join('');
                });
        }

        function removeFromWishlist(dest_id) {
            const formData = new FormData();
            formData.append('destination_id', dest_id);

            fetch('api-wishlist.php?action=remove', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        loadWishlist();
                    }
                });
        }

        document.addEventListener('DOMContentLoaded', loadWishlist);
    </script>
</body>
</html>
