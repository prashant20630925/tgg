<?php

require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search & Filter Destinations</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/modern.css">
    <link rel="stylesheet" href="./css/theme-color.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .search-header { margin-bottom: 40px; }
        .search-header h1 { margin-bottom: 20px; }
        .filter-container { display: grid; grid-template-columns: 250px 1fr; gap: 30px; margin-bottom: 30px; }
        .filter-panel { background: white; padding: 20px; border-radius: 8px; height: fit-content; }
        .filter-group { margin-bottom: 20px; }
        .filter-group label { display: block; font-weight: 600; margin-bottom: 10px; }
        .filter-group input, .filter-group select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .search-input { width: 100%; padding: 12px; font-size: 16px; border: 1px solid #ddd; border-radius: 4px; }
        .btn-filter { background: #1e73be; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-weight: 600; }
        .btn-filter:hover { background: #bcb528; }
        .results-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px; }
        .card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .card-image { width: 100%; height: 200px; object-fit: cover; }
        .card-content { padding: 15px; }
        .card-title { font-weight: 600; margin-bottom: 5px; }
        .card-location { font-size: 12px; color: #999; }
        .card-rating { color: #ffc107; margin: 8px 0; }
        .card-link { color: #1e73be; text-decoration: none; font-size: 13px; font-weight: 600; }
        .card-link:hover { text-decoration: underline; }
        .empty { text-align: center; padding: 60px 20px; }
        .loading { text-align: center; padding: 40px; }
        @media (max-width: 768px) {
            .filter-container { grid-template-columns: 1fr; }
            .results-container { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="breadcrumb" style="margin-bottom: 20px; font-size: 14px;">
            <a href="mainpage.php" style="color: #1e73be; text-decoration: none;">Home</a> / Search & Filter Destinations
        </div>
        <div class="search-header">
            <h1>🔍 Search & Filter Destinations</h1>
        </div>

        <div class="filter-container">
            <div class="filter-panel">
                <div class="filter-group">
                    <label>Search by Name</label>
                    <input type="text" id="search-input" placeholder="e.g., Kathmandu, Pokhara..." class="search-input">
                </div>

                <div class="filter-group">
                    <label>Minimum Rating</label>
                    <select id="rating-filter">
                        <option value="">All Ratings</option>
                        <option value="5">★★★★★ (5 stars)</option>
                        <option value="4">★★★★☆ (4+ stars)</option>
                        <option value="3">★★★☆☆ (3+ stars)</option>
                        <option value="2">★★☆☆☆ (2+ stars)</option>
                        <option value="1">★☆☆☆☆ (1+ stars)</option>
                    </select>
                </div>

                <button class="btn-filter" onclick="applyFilters()">Apply Filters</button>
            </div>

            <div class="results-container" id="results-container">
                <div class="loading">Loading destinations...</div>
            </div>
        </div>
    </div>

    <script>
        function applyFilters() {
            const search = document.getElementById('search-input').value.trim();
            const rating = document.getElementById('rating-filter').value;

            let url = 'api-filter.php?action=filter';
            if (search) url += `&search=${encodeURIComponent(search)}`;
            if (rating) url += `&rating_min=${rating}`;

            document.getElementById('results-container').innerHTML = '<div class="loading">Loading...</div>';

            fetch(url)
                .then(r => r.json())
                .then(data => {
                    const container = document.getElementById('results-container');
                    if (!data.destinations || data.destinations.length === 0) {
                        container.innerHTML = '<div class="empty" style="grid-column: 1/-1;"><p>No destinations found. Try different filters.</p></div>';
                        return;
                    }
                    container.innerHTML = data.destinations.map(d => {
                        const stars = d.avg_rating > 0 ? '★'.repeat(Math.round(d.avg_rating)) + '☆'.repeat(5 - Math.round(d.avg_rating)) : 'No ratings';
                        return `
                            <div class="card">
                                <img src="images/destination/${d.image}" alt="${d.name}" class="card-image">
                                <div class="card-content">
                                    <div class="card-title">${d.name}</div>
                                    <div class="card-location">📍 ${d.location}</div>
                                    <div class="card-rating">${stars} (${d.total_reviews})</div>
                                    <a href="destination-details-enhanced.php?id=${d.id}" class="card-link">View Details →</a>
                                </div>
                            </div>
                        `;
                    }).join('');
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('results-container').innerHTML = '<div class="empty" style="grid-column: 1/-1;"><p>Error loading destinations</p></div>';
                });
        }

        // Load on page init — seed search input from GET param if present
        document.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);
            const q = params.get('query') || params.get('search') || '';
            if (q) {
                const input = document.getElementById('search-input');
                if (input) input.value = q;
            }
            applyFilters();

            // Real-time search
            const searchEl = document.getElementById('search-input');
            const ratingEl = document.getElementById('rating-filter');
            if (searchEl) searchEl.addEventListener('keyup', applyFilters);
            if (ratingEl) ratingEl.addEventListener('change', applyFilters);
        });
    </script>
</body>
</html>
