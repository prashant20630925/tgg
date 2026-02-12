<?php
// Include authentication check - redirects if not logged in
require_once 'auth_check.php';
include "admin/admin-config.php";

// Fetch user name for welcome message
$user_name = '';
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT fullname FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $user_result = $stmt->get_result();
    if ($row = $user_result->fetch_assoc()) {
        $user_name = $row['fullname'];
    }
    $stmt->close();
}

// Fetch latest 4 destinations
$result = $conn->query("SELECT * FROM destinations ORDER BY id DESC LIMIT 4");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="./css/style.css">

<style>
/* ========================================
   MAINPAGE CSS - ORGANIZED & OPTIMIZED
   ======================================== */

/* ===== ROOT VARIABLES ===== */
:root {
  --primary-blue: #1e73be;
  --primary-blue-dark: #125a94;
  --success-green: #2ecc71;
  --success-green-dark: #27ae60;
  --accent-teal: #30d2cf;
  --accent-dark-blue: #016aa0;
  --white: #fff;
  --text-dark: #333;
  --text-gray: #999;
  --bg-light-gray: #f0f0f0;
  --transition-normal: 0.3s ease;
  --transition-slow: 0.4s ease;
  --border-radius-md: 5px;
  --border-radius-lg: 12px;
  --border-radius-full: 25px;
}

/* ===== HEADER - MAIN CONTAINER ===== */
.main {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: auto !important;
  width: 100% !important;
  max-width: 100% !important;
  display: flex !important;
  flex-direction: row !important;
  align-items: center;
  justify-content: space-between;
  background: linear-gradient(305deg, rgba(48, 210, 207, 0.15) 30%, rgba(1, 106, 160, 0.15) 82%) !important;
  overflow: visible !important;
  z-index: 1000;
  background-size: 100% 100%;
  background-attachment: fixed;
  padding: 12px 20px;
  gap: 30px;
  flex-wrap: nowrap;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

/* ===== HEADER - TOP ROW (LOGO + SEARCH) ===== */
.list {
  display: flex;
  align-items: center;
  gap: 15px;
  position: static !important;
  float: none !important;
  width: 100%;
  padding: 12px 20px;
  background: transparent;
}

/* Add margin to header to account for fixed main */
header {
  margin-top: auto;
}

/* ===== LOGO SECTION ===== */
.logo {
  display: flex;
  align-items: center;
  gap: 15px;
  flex: 1;
  flex-wrap: nowrap;
}

.logo a {
  display: flex;
  align-items: center;
  flex-shrink: 0;
}

.logo a img {
  width: 50px;
  height: 50px;
  transition: transform var(--transition-normal);
  position: static !important;
  float: none !important;
  top: auto !important;
  left: auto !important;
  margin: 0 !important;
  animation: none !important;
}

.logo a img:hover {
  transform: scale(1.1) rotate(-5deg);
}

/* ===== SEARCH BOX ===== */
.search-box {
  display: flex;
  align-items: center;
  gap: 8px;
  flex: 1;
  min-width: 200px;
  max-width: 400px;
}

.search-box input {
  flex: 1;
  padding: 10px 14px;
  border: 2px solid var(--white);
  border-radius: var(--border-radius-full);
  font-size: 14px;
  outline: none;
  transition: all var(--transition-normal);
  background: rgba(255, 255, 255, 0.95);
}

.search-box input:focus {
  background: var(--white);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.search-box input::placeholder {
  color: var(--text-gray);
}

.search-box button {
  padding: 10px 14px;
  cursor: pointer;
  border: none;
  background: linear-gradient(135deg, var(--primary-blue), var(--primary-blue-dark));
  color: var(--white);
  border-radius: 50%;
  font-size: 18px;
  transition: all var(--transition-normal);
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.search-box button:hover {
  transform: scale(1.1);
  box-shadow: 0 4px 12px rgba(30, 115, 190, 0.4);
}

/* ===== HEADER - NAVIGATION MENU ===== */
.list2 {
  display: flex;
  align-items: center;
  gap: 0;
  position: static !important;
  float: none !important;
  flex-wrap: nowrap;
  width: auto;
  justify-content: flex-end;
  margin: 0 !important;
  padding: 0 !important;
  background: transparent;
  list-style: none;
}

.list2 li {
  display: inline-block !important;
  float: none !important;
  list-style: none;
  margin: 0;
  padding: 0;
}

.list2 li a {
  text-decoration: none;
  color: var(--white);
  padding: 10px 20px;
  border: 1px solid transparent;
  transition: var(--transition-normal);
  display: inline-block;
  font-weight: 600;
  font-size: 15px;
}

.list2 li a:hover {
  background-color: rgba(255, 255, 255, 0.2);
  color: var(--white);
  border-bottom: 2px solid var(--white);
}

.list2 li.active-menu a {
  background-color: rgba(255, 255, 255, 0.3);
  color: var(--white);
  border-bottom: 3px solid var(--white);
}

/* ===== SIGNUP BUTTON (IF EXISTS) ===== */
.signup-op a {
  text-decoration: none;
  border: 2px solid #000;
  background-color: rgba(68, 241, 154, 1);
  padding: 8px 20px;
  color: black;
  transition: var(--transition-slow);
}

.signup-op a:hover {
  background: var(--white);
}

/* Add margin to header to account for fixed main */
header {
  margin-top: 120px;
}

body {
  padding: 0;
  margin: 0;
}

/* ========================================
   DESTINATIONS SECTION
   ======================================== */

/* ===== DESTINATIONS CONTAINER ===== */
.contain {
  background: linear-gradient(135deg, var(--accent-teal) 0%, var(--accent-dark-blue) 100%);
  width: 100%;
  padding: 50px 20px;
  margin: 0;
  min-height: auto;
}

/* ===== DESTINATIONS HEADING ===== */
.contain .heading {
  text-align: center;
  margin-bottom: 40px;
}

.contain .heading h2 {
  color: var(--white);
  text-transform: uppercase;
  font-size: 32px;
  font-weight: 700;
  letter-spacing: 2px;
  font-family: 'Arial', sans-serif;
  border-bottom: 3px solid var(--white);
  padding-bottom: 15px;
  display: inline-block;
}

/* ===== DESTINATIONS GRID ===== */
.destinations-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 25px;
  max-width: 1400px;
  margin: 0 auto 40px;
  padding: 0 20px;
}

/* ===== DESTINATION CARD ===== */
.contain .box {
  background: var(--white);
  border-radius: var(--border-radius-lg);
  overflow: hidden;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
  transition: all var(--transition-slow);
  cursor: pointer;
  position: relative;
  height: 400px;
  display: flex;
  flex-direction: column;
}

.contain .box:hover {
  transform: translateY(-10px);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
}

/* ===== DESTINATION CARD - IMAGE ===== */
.contain .box .imgBox {
  overflow: hidden;
  flex: 1;
  background: var(--bg-light-gray);
}

.contain .box .imgBox img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform var(--transition-slow);
}

.contain .box:hover .imgBox img {
  transform: scale(1.1);
}

/* ===== DESTINATION CARD - CONTENT ===== */
.contain .box .name-text {
  padding: 20px;
  background: var(--white);
  position: static;
  color: var(--text-dark);
}

.contain .box .name-text h2 {
  margin: 0 0 15px 0;
  font-size: 20px;
  color: var(--primary-blue);
  font-weight: 700;
  border: none;
  text-align: left;
  text-transform: none;
  padding: 0;
}

.contain .box .name-text p {
  margin: 0 0 10px 0;
  color: var(--text-gray);
  font-size: 13px;
}

.contain .box .name-text a {
  display: inline-block;
  background: linear-gradient(135deg, var(--success-green) 0%, var(--success-green-dark) 100%);
  color: var(--white);
  padding: 10px 20px;
  border-radius: var(--border-radius-md);
  text-decoration: none;
  font-weight: 600;
  transition: all var(--transition-normal);
  margin-top: 10px;
  border: none;
}

.contain .box .name-text a:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(46, 204, 113, 0.3);
  background: var(--success-green-dark);
  color: var(--white);
}

/* ===== VIEW ALL BUTTON ===== */
.destination-btn {
  text-align: center;
  margin-top: 20px;
  padding-bottom: 100px;
  margin-bottom: 100px;
}

.destination-btn a {
  display: inline-block;
  background: var(--white);
  color: var(--accent-dark-blue);
  padding: 12px 35px;
  border-radius: var(--border-radius-full);
  text-decoration: none;
  font-weight: 700;
  transition: all var(--transition-normal);
  border: 2px solid var(--white);
  font-size: 16px;
}

.destination-btn a:hover {
  background: transparent;
  color: var(--white);
  transform: scale(1.05);
}

/* ========================================
   RESPONSIVE DESIGN
   ======================================== */

/* Large screens (1440px and above) */
@media (min-width: 1440px) {
  .contain {
    padding: 60px 40px;
  }

  .destinations-grid {
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    max-width: 1600px;
  }

  .contain .heading h2 {
    font-size: 36px;
    letter-spacing: 3px;
  }

  .contain .box {
    height: 420px;
  }
}

/* Tablets (1024px and below) */
@media (max-width: 1024px) {
  .contain {
    padding: 45px 25px;
  }

  .destinations-grid {
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 22px;
    max-width: 1200px;
    padding: 0 15px;
  }

  .contain .heading h2 {
    font-size: 28px;
    letter-spacing: 1px;
  }

  .contain .box {
    height: 380px;
  }

  .contain .box .name-text h2 {
    font-size: 18px;
  }

  .destination-btn a {
    padding: 11px 30px;
    font-size: 15px;
  }
}

/* Medium devices (768px and below) */
@media (max-width: 768px) {
  .contain {
    padding: 35px 15px;
  }

  .destinations-grid {
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 14px;
    padding: 0 10px;
    max-width: 100%;
  }

  .contain .heading {
    margin-bottom: 30px;
  }

  .contain .heading h2 {
    font-size: 22px;
    letter-spacing: 0px;
    padding-bottom: 12px;
  }

  .contain .box {
    height: 320px;
  }

  .contain .box .name-text {
    padding: 14px;
  }

  .contain .box .name-text h2 {
    font-size: 16px;
    margin-bottom: 8px;
  }

  .contain .box .name-text p {
    font-size: 12px;
    margin-bottom: 8px;
  }

  .contain .box .name-text a {
    padding: 7px 14px;
    font-size: 12px;
    margin-top: 8px;
  }

  .search-box {
    min-width: 150px;
    max-width: 300px;
  }

  .destination-btn {
    margin-top: 15px;
    padding-bottom: 40px;
    margin-bottom: 40px;
  }

  .destination-btn a {
    padding: 10px 25px;
    font-size: 14px;
  }
}

/* Small tablets (640px and below) */
@media (max-width: 640px) {
  .contain {
    padding: 25px 12px;
  }

  .destinations-grid {
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 12px;
    padding: 0;
  }

  .contain .heading {
    margin-bottom: 20px;
  }

  .contain .heading h2 {
    font-size: 18px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--white);
  }

  .contain .box {
    height: 280px;
  }

  .contain .box .name-text {
    padding: 12px;
  }

  .contain .box .name-text h2 {
    font-size: 14px;
    margin-bottom: 6px;
  }

  .contain .box .name-text p {
    font-size: 11px;
    margin-bottom: 6px;
  }

  .contain .box .name-text a {
    padding: 6px 12px;
    font-size: 11px;
  }
}

/* Mobile devices (480px and below) */
@media (max-width: 480px) {
  .contain {
    padding: 20px 10px;
  }

  .destinations-grid {
    grid-template-columns: 1fr;
    gap: 12px;
    padding: 0;
  }

  .contain .heading {
    margin-bottom: 20px;
  }

  .contain .heading h2 {
    font-size: 16px;
    padding-bottom: 10px;
    letter-spacing: 0.5px;
  }

  .contain .box {
    height: 260px;
  }

  .contain .box .name-text {
    padding: 10px;
  }

  .contain .box .name-text h2 {
    font-size: 13px;
    margin-bottom: 5px;
  }

  .contain .box .name-text p {
    font-size: 11px;
    margin-bottom: 5px;
  }

  .contain .box .name-text a {
    padding: 6px 10px;
    font-size: 11px;
    margin-top: 5px;
  }

  .destination-btn {
    margin-top: 10px;
    padding-bottom: 30px;
    margin-bottom: 30px;
  }

  .destination-btn a {
    padding: 9px 20px;
    font-size: 13px;
  }

  .list {
    flex-wrap: wrap;
    gap: 10px;
  }

  .logo {
    width: 100%;
    justify-content: space-between;
  }

  .search-box {
    min-width: 100%;
    max-width: 100%;
  }

  .list2 {
    flex-wrap: wrap;
  }

  .list2 li a {
    padding: 8px 15px;
    font-size: 13px;
  }
}
</style>
</head>

<body>

<header >
<div class="slideshow-container">

<div class="mySlides">
  <img src="./images/coverpage/cover2.jpg" style="width:100%">
</div>

<div class="mySlides">
  <img src="./images/coverpage/cover1.jpg" style="width:100%">
</div>

<div class="mySlides">
  <img src="./images/coverpage/cover3.jpg" style="width:100%">
</div>

<div class="main">

<!-- 🔹 LOGO + SEARCH (SAME LINE) -->
<ul class="list">
  <li class="logo">
    <a href="mainPage.php">
      <img src="./images/logo/logo.png" style="width:50px;height:50px">
    </a>

    <form action="search-destinations.php" method="GET" class="search-box">
      <input type="text" name="query" placeholder="e.g., Kathmandu, Pokhara..." required>
      <button type="submit">🔍</button>
    </form>
  </li>
</ul>

<!-- 🔹 MENU -->
<ul class="list2">
  <li class="active-menu"><a href="mainPage.php">Home</a></li>
  <li><a href="destination.php">Destination</a></li>
  <li><a href="search-destinations.php">Search & Filter</a></li>
  <li><a href="my-wishlist.php">My Wishlist</a></li>
  <li><a href="gallery.html">Gallery</a></li>
  <li><a href="feedback.php">Feedback</a></li>
  <li><a href="logout.php">Logout</a></li>
</ul>

</div>

<a class="prev" onclick="plusSlides(-1)">&#10094;</a>
<a class="next" onclick="plusSlides(1)">&#10095;</a>

</div>
</header>

<!-- Welcome Message -->
<?php if (!empty($user_name)): ?>
<div style="background: linear-gradient(135deg, var(--primary-blue), var(--primary-blue-dark)); color: var(--white); text-align: center; padding: 15px 20px; font-size: 18px; font-weight: 600; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
  Welcome back, <?php echo htmlspecialchars($user_name); ?>! 🎉
</div>
<?php endif; ?>

<!-- 🌍 DESTINATIONS -->
<div class="contain">
  <div class="heading">
    <h2>🗺️ Places You must Visit</h2>
  </div>

  <div class="destinations-grid">
    <?php while($row = $result->fetch_assoc()) { ?>
    <div class="box">
      <div class="imgBox">
        <img src="./images/destination/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
      </div>
      <div class="name-text">
        <h2><?php echo htmlspecialchars($row['name']); ?></h2>
        <p style="color: #666; font-size: 13px; margin: 0 0 10px 0;">📍 <?php echo htmlspecialchars($row['location']); ?></p>
        <a href="destination-details-enhanced.php?id=<?php echo $row['id']; ?>">✨ View details →</a>
      </div>
    </div>
    <?php } ?>
  </div>

  <div class="destination-btn">
    <a href="destination.php">View All Destinations</a>
  </div>
</div>

<br>

<!-- 🔻 FOOTER -->
<footer class="tguide-footer">
<div class="footer-container">

<div class="footer-section brand">
<img src="./images/logo/logo.png" class="footer-logo">
<p>Discover Nepal.</p>
</div>

<div class="footer-section">
<h3>Explore</h3>
<a href="#">Places</a>
<a href="#">Hotels</a>
<a href="#">Locations</a>
</div>

<div class="footer-section">
<h3>Community</h3>
<a href="#">Share Experience</a>
<a href="#">Join Us</a>
<a href="#">Blog</a>
</div>

<div class="footer-section">
<h3>Connect</h3>
<a href="#">Instagram</a>
<a href="#">Facebook</a>
<a href="#">YouTube</a>
</div>

</div>

<p class="footer-bottom">
© 2025 TGuide. Made for travelers exploring Nepal.
</p>
</footer>

<!-- 🎞 SLIDER SCRIPT -->
<script>
var slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n){ showSlides(slideIndex += n); }
function currentSlide(n){ showSlides(slideIndex = n); }

function showSlides(n){
  var i;
  var slides=document.getElementsByClassName("mySlides");
  var dots=document.getElementsByClassName("dot");
  if(n>slides.length){slideIndex=1}
  if(n<1){slideIndex=slides.length}
  for(i=0;i<slides.length;i++){slides[i].style.display="none"}
  for(i=0;i<dots.length;i++){dots[i].className=dots[i].className.replace(" active","")}
  slides[slideIndex-1].style.display="block";
  dots[slideIndex-1].className+=" active";
}
</script>

</body>
</html>
