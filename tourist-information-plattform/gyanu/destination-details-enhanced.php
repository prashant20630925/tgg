<?php

require_once 'auth_check.php';
include "config.php";

$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {
    die("Invalid destination");
}

$stmt = $conn->prepare("SELECT * FROM destinations WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$destination = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$destination) {
    die("Destination not found");
}

// Get ratings
$rating_stmt = $conn->prepare("SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews FROM reviews WHERE destination_id = ?");
$rating_stmt->bind_param("i", $id);
$rating_stmt->execute();
$rating_data = $rating_stmt->get_result()->fetch_assoc();
$rating_stmt->close();

$avg_rating = round($rating_data['avg_rating'] ?? 0, 1);
$total_reviews = $rating_data['total_reviews'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($destination['name']); ?> - Destination Details</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/modern.css">
    <link rel="stylesheet" href="./css/theme-color.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .breadcrumb { margin-bottom: 20px; font-size: 14px; }
        .breadcrumb a { color: #1e73be; text-decoration: none; }
        .hero-section { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px; }
        .hero-image img { width: 100%; border-radius: 10px; }
        .hero-content h1 { font-size: 36px; margin-bottom: 15px; }
        .rating-bar { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; }
        .stars { font-size: 20px; color: #ffc107; }
        .rating-text { font-size: 14px; color: #666; }
        .wishlist-btn { background: #1e73be; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-bottom: 20px; }
        .wishlist-btn.added { background: #e74c3c; }
        .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px; }
        .info-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .info-label { font-size: 12px; color: #999; text-transform: uppercase; margin-bottom: 5px; }
        .info-value { font-size: 16px; font-weight: 600; }
        .map-section { background: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        .map-section h2 { margin-bottom: 15px; }
        .map-iframe { width: 100%; height: 400px; border: none; border-radius: 8px; }
        .reviews-section { background: white; padding: 30px; border-radius: 8px; }
        .review-form { margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid #eee; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit; }
        .form-group textarea { resize: vertical; min-height: 100px; }
        .rating-input { display: flex; gap: 5px; }
        .rating-input input { display: none; }
        .rating-input label { margin-bottom: 0; cursor: pointer; font-size: 24px; color: #ddd; }
        .rating-input input:checked ~ label, .rating-input label:hover ~ input + label { color: #ffc107; }
        .submit-btn { background: #2ecc71; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: 600; }
        .submit-btn:hover { background: #27ae60; }
        .reviews-list { margin-top: 30px; }
        .review-item { padding: 20px; border: 1px solid #eee; border-radius: 8px; margin-bottom: 15px; }
        .review-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .review-author { font-weight: 600; }
        .review-rating { color: #ffc107; }
        .review-date { font-size: 12px; color: #999; }
        .review-text { color: #333; line-height: 1.5; }
        .recommendations { margin-top: 40px; }
        .recommendations h2 { margin-bottom: 20px; }
        .rec-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .rec-card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .rec-card:hover { transform: translateY(-5px); }
        .rec-image { width: 100%; height: 180px; object-fit: cover; }
        .rec-content { padding: 15px; }
        .rec-name { font-weight: 600; margin-bottom: 5px; }
        .rec-rating { font-size: 12px; color: #ffc107; }
        @media (max-width: 768px) {
            .hero-section { grid-template-columns: 1fr; }
            .info-grid { grid-template-columns: 1fr; }
            .hero-content h1 { font-size: 24px; }
            .map-iframe { height: 300px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="breadcrumb">
            <a href="mainpage.php">Home</a> / <a href="destination.php">Destinations</a> / <?php echo htmlspecialchars($destination['name']); ?>
        </div>

        <div class="hero-section">
            <div class="hero-image">
                <img src="images/destination/<?php echo htmlspecialchars($destination['image']); ?>" alt="<?php echo htmlspecialchars($destination['name']); ?>">
            </div>
            <div class="hero-content">
                <h1><?php echo htmlspecialchars($destination['name']); ?></h1>
                
                <div class="rating-bar">
                    <div class="stars" id="rating-stars">★★★★★</div>
                    <div class="rating-text">
                        <strong><?php echo $avg_rating; ?></strong> out of 5 
                        <span style="color: #999;">(<?php echo $total_reviews; ?> reviews)</span>
                    </div>
                </div>

                <button class="wishlist-btn" id="wishlist-btn" onclick="toggleWishlist(<?php echo $id; ?>)">
                    ♥ Add to Wishlist
                </button>

                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-label">Location</div>
                        <div class="info-value"><?php echo htmlspecialchars($destination['location']); ?></div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">Destination Type</div>
                        <div class="info-value">Tourist Spot</div>
                    </div>
                </div>

                <p style="line-height: 1.6; color: #555;">
                    <?php echo htmlspecialchars($destination['description']); ?>
                </p>
            </div>
        </div>

        <!-- Google Maps Section -->
        <div class="map-section">
            <h2>📍 Location on Map</h2>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3416950.4133181525!2d81.48885223770556!3d28.371983318666004!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3995e8c77d2e68cf%3A0x34a29abcd0cc86de!2sNepal!5e1!3m2!1sen!2snp!4v1770731992917!5m2!1sen!2snp" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe> width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>

        <!-- Reviews & Ratings Section -->
        <div class="reviews-section">
            <h2>✍️ Reviews & Ratings</h2>

            <?php if (isset($_SESSION['user_id'])): ?>
            <div class="review-form">
                <h3>Share Your Experience</h3>
                <form id="review-form" onsubmit="submitReview(event, <?php echo $id; ?>)">
                    <div class="form-group">
                        <label>Rating</label>
                        <div class="rating-input">
                            <input type="radio" id="star5" name="rating" value="5" required>
                            <label for="star5">★</label>
                            <input type="radio" id="star4" name="rating" value="4">
                            <label for="star4">★</label>
                            <input type="radio" id="star3" name="rating" value="3">
                            <label for="star3">★</label>
                            <input type="radio" id="star2" name="rating" value="2">
                            <label for="star2">★</label>
                            <input type="radio" id="star1" name="rating" value="1">
                            <label for="star1">★</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="review-text">Your Review (Optional)</label>
                        <textarea id="review-text" name="review_text" placeholder="Share your thoughts about this destination..."></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Submit Review</button>
                </form>
            </div>
            <?php endif; ?>

            <div class="reviews-list" id="reviews-container">
                <p>Loading reviews...</p>
            </div>
        </div>

        <!-- Recommendations Section -->
        <div class="recommendations">
            <h2>🌟 Similar Destinations You Might Like</h2>
            <div class="rec-grid" id="recommendations-container">
                <p>Loading recommendations...</p>
            </div>
        </div>
    </div>

    <script>
        const destination_id = <?php echo $id; ?>;
        const user_id = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;

        // Load reviews
        function loadReviews() {
            fetch(`api-reviews.php?action=get_reviews&destination_id=${destination_id}`)
                .then(r => r.json())
                .then(data => {
                    const container = document.getElementById('reviews-container');
                    if (data.reviews.length === 0) {
                        container.innerHTML = '<p style="color: #999;">No reviews yet. Be the first to review!</p>';
                        return;
                    }
                    container.innerHTML = data.reviews.map(r => `
                        <div class="review-item">
                            <div class="review-header">
                                <span class="review-author">${r.fullname}</span>
                                <span class="review-rating">${'★'.repeat(r.rating)}${'☆'.repeat(5-r.rating)}</span>
                            </div>
                            <div class="review-date">${new Date(r.created_at).toLocaleDateString()}</div>
                            <div class="review-text">${r.review_text || '(No text review)'}</div>
                        </div>
                    `).join('');
                });
        }

        // Submit review
        function submitReview(e, dest_id) {
            e.preventDefault();
            const form = document.getElementById('review-form');
            const formData = new FormData(form);
            formData.append('destination_id', dest_id);

            fetch('api-reviews.php?action=add_review', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        alert('Review submitted!');
                        form.reset();
                        loadReviews();
                    } else {
                        alert('Error: ' + data.error);
                    }
                });
        }

        // Wishlist toggle
        function toggleWishlist(dest_id) {
            if (!user_id) {
                alert('Please log in to use wishlist');
                return;
            }

            const btn = document.getElementById('wishlist-btn');
            const action = btn.classList.contains('added') ? 'remove' : 'add';

            const formData = new FormData();
            formData.append('destination_id', dest_id);

            fetch(`api-wishlist.php?action=${action}`, { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        btn.classList.toggle('added');
                        btn.textContent = action === 'add' ? '♥ Remove from Wishlist' : '♥ Add to Wishlist';
                    }
                });
        }

        // Load recommendations
        function loadRecommendations() {
            fetch(`api-recommendations.php?action=get_similar&destination_id=${destination_id}&limit=3`)
                .then(r => r.json())
                .then(data => {
                    const container = document.getElementById('recommendations-container');
                    if (data.similar.length === 0) {
                        container.innerHTML = '<p>No recommendations available</p>';
                        return;
                    }
                    container.innerHTML = data.similar.map(d => `
                        <div class="rec-card">
                            <img src="images/destination/${d.image}" alt="${d.name}" class="rec-image">
                            <div class="rec-content">
                                <div class="rec-name">${d.name}</div>
                                <div class="rec-rating">${d.avg_rating > 0 ? '★'.repeat(Math.round(d.avg_rating)) : 'No ratings'}</div>
                                <a href="destination-details-enhanced.php?id=${d.id}" style="color: #1e73be; text-decoration: none; font-size: 12px;">View Details →</a>
                            </div>
                        </div>
                    `).join('');
                });
        }

        // Check wishlist status
        function checkWishlist() {
            if (!user_id) return;
            fetch(`api-wishlist.php?action=check&destination_id=${destination_id}`)
                .then(r => r.json())
                .then(data => {
                    const btn = document.getElementById('wishlist-btn');
                    if (data.in_wishlist) {
                        btn.classList.add('added');
                        btn.textContent = '♥ Remove from Wishlist';
                    }
                });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            loadReviews();
            loadRecommendations();
            checkWishlist();

            // Update star rating
            const ratingStars = document.getElementById('rating-stars');
            if (ratingStars && <?php echo $avg_rating; ?> > 0) {
                const fullStars = Math.round(<?php echo $avg_rating; ?>);
                ratingStars.textContent = '★'.repeat(fullStars) + '☆'.repeat(5 - fullStars);
            }
        });
    </script>
</body>
</html>
