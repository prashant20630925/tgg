<?php
// Include authentication check - redirects if not logged in
require_once 'auth_check.php';
include "config.php";

$id = $_GET["id"];

$sql = "SELECT * FROM destinations WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
?>

<h2><?php echo $data["name"]; ?></h2>
<p><?php echo $data["description"]; ?></p>
<p>Location: <?php echo $data["location"]; ?></p>

<img src="images/<?php echo $data["image"]; ?>" width="300"> -->


<?php
include "config.php";

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM destinations WHERE id=$id");
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($row['name']); ?> - Destination Details</title>

<style>
/* ===== CSS VARIABLES (THEME) ===== */
:root {
    --primary-color: #1e73be;
    --primary-dark: #125a94;
    --success-color: #2ecc71;
    --light-bg: #f4f6f9;
    --white: #ffffff;
    --dark-text: #333;
    --gray-text: #666;
    --border-color: #ddd;
    --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.15);
    --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.2);
    --border-radius: 10px;
    --transition: all 0.3s ease;
}

/* ===== GLOBAL STYLES ===== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html, body {
    height: 100%;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, var(--light-bg) 0%, #e8ecf1 100%);
    color: var(--dark-text);
    line-height: 1.6;
    min-height: 100vh;
    padding-top: 80px;
}

/* ===== CONTAINER ===== */
.container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 40px 20px;
}

/* ===== BACK BUTTON ===== */
.back-button {
    display: inline-block;
    margin-bottom: 30px;
    padding: 12px 24px;
    background: var(--primary-color);
    color: var(--white);
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: var(--transition);
    box-shadow: var(--shadow-md);
}

.back-button:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

/* ===== DESTINATION CARD ===== */
.destination-card {
    background: var(--white);
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
    margin-bottom: 30px;
}

.destination-card .image-container {
    width: 100%;
    height: 400px;
    overflow: hidden;
    background: var(--light-bg);
}

.destination-card .image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.destination-card:hover .image-container img {
    transform: scale(1.05);
}

.destination-card .content {
    padding: 40px;
}

/* ===== DESTINATION TITLE ===== */
.destination-card h1 {
    font-size: 36px;
    color: var(--primary-color);
    margin-bottom: 15px;
    font-weight: 700;
    letter-spacing: -0.5px;
}

/* ===== DESTINATION INFO ===== */
.info-section {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    margin-bottom: 30px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid var(--primary-color);
}

.info-item {
    flex: 1;
    min-width: 200px;
}

.info-item label {
    font-weight: 700;
    color: var(--primary-color);
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: block;
    margin-bottom: 8px;
}

.info-item p {
    font-size: 16px;
    color: var(--dark-text);
}

/* ===== DESTINATION DESCRIPTION ===== */
.description-section {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 8px;
    margin-bottom: 30px;
}

.description-section h2 {
    color: var(--primary-color);
    font-size: 22px;
    margin-bottom: 15px;
    font-weight: 700;
}

.description-section p {
    color: var(--gray-text);
    line-height: 1.8;
    font-size: 15px;
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
    body {
        padding-top: 140px;
    }

    .container {
        padding: 30px 15px;
    }

    .destination-card .content {
        padding: 30px 20px;
    }

    .destination-card h1 {
        font-size: 28px;
        margin-bottom: 12px;
    }

    .destination-card .image-container {
        height: 300px;
    }

    .info-section {
        flex-direction: column;
        gap: 15px;
        padding: 15px;
    }

    .info-item {
        min-width: 100%;
    }

    .description-section {
        padding: 20px;
    }

    .description-section h2 {
        font-size: 18px;
    }

    .description-section p {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    body {
        padding-top: 120px;
    }

    .container {
        padding: 20px 10px;
    }

    .destination-card .content {
        padding: 20px 15px;
    }

    .destination-card h1 {
        font-size: 22px;
        margin-bottom: 10px;
    }

    .destination-card .image-container {
        height: 250px;
    }

    .info-section {
        padding: 12px;
    }

    .info-item label {
        font-size: 12px;
        margin-bottom: 6px;
    }

    .info-item p {
        font-size: 14px;
    }

    .description-section {
        padding: 15px;
    }

    .description-section h2 {
        font-size: 16px;
        margin-bottom: 10px;
    }

    .description-section p {
        font-size: 13px;
    }

    .back-button {
        padding: 10px 20px;
        font-size: 14px;
    }
}
</style>

</head>
<body>

<div class="container">
    <a href="mainPage.php" class="back-button">← Back to Home</a>

    <div class="destination-card">
        <div class="image-container">
            <img src="./images/destination/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
        </div>

        <div class="content">
            <h1>🗺️ <?php echo htmlspecialchars($row['name']); ?></h1>

            <div class="info-section">
                <div class="info-item">
                    <label>📍 Location</label>
                    <p><?php echo htmlspecialchars($row['location']); ?></p>
                </div>
            </div>

            <div class="description-section">
                <h2>About This Destination</h2>
                <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
            </div>
        </div>
    </div>
</div>

</body>
</html>

