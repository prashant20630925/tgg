<?php
// Include authentication check - redirects if not logged in
require_once 'auth_check.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Home - Explore Nepal</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body,html{height:100%;font-family:'Poppins',sans-serif;color:white;}

    body{
      background:url('./images/explorenepal.jpg') no-repeat center center/cover;
      display:flex;
      align-items:center;
      justify-content:center;
      flex-direction:column;
      text-align:center;
      -webkit-backdrop-filter: blur(10px);
      backdrop-filter: blur(10px);
    }

    .overlay{
      position:absolute;
      top:0;left:0;right:0;bottom:0;
      background:rgba(0,0,0,0.5);
      z-index:1;
    }

    .header-top {
      position: absolute;
      top: 20px;
      right: 30px;
      z-index: 10;
      display: flex;
      gap: 20px;
      align-items: center;
    }

    .user-info {
      background: rgba(255, 255, 255, 0.1);
      padding: 10px 15px;
      border-radius: 8px;
      backdrop-filter: blur(10px);
      font-size: 14px;
    }

    .logout-btn {
      background: #ff4444;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .logout-btn:hover {
      background: #cc0000;
      transform: scale(1.05);
    }

    .content{
      position:relative;
      z-index:2;
      padding:20px;
      max-width:600px;
    }

    .hero {
      position: absolute;
      top: 40px;
      left: 40px;
      display: flex;
      align-items: center;
      gap: 15px;
      font-family: 'Poppins', sans-serif;
      color: white;
      z-index: 10;
    }

    .hero img {
      width: 70px;
      filter: brightness(1.5);
      border-radius: 50%;
      object-fit: cover;
    }

    .hero h2 {
      font-size: 32px;
      font-weight: 600;
      letter-spacing: 1px;
      color: white;
    }

    h1{
      font-size:3rem;
      font-weight:700;
      letter-spacing:1px;
      margin-bottom:16px;
      font-family: 'Times New Roman', Times, serif;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
      color: rgb(186, 201, 201);
    }
   
    p{
      font-size:1.2rem;
      font-weight:300;
      margin-bottom:28px;
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.6);
      color: #ffffff;
    }

    .btn{
      background:#4967ea;
      color:#000;
      border:none;
      border-radius:8px;
      padding:14px 32px;
      font-size:1.1rem;
      font-weight:500;
      cursor:pointer;
      transition:all 0.3s ease;
    }

    .btn:hover{
      background:#000;
      color:#fff;
      transform:scale(1.05);
    }

    .btn-box{
      display:flex;
      gap:20px;
      justify-content:center;
    }

    @media (max-width:600px){
      h1{font-size:2.2rem}
      p{font-size:1rem}
      .header-top {
        top: 10px;
        right: 15px;
        flex-direction: column;
        gap: 10px;
      }
    }
  </style>
</head>
<body>
  <div class="header-top">
    <div class="user-info">
      Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
    </div>
    <a href="logout.php" class="logout-btn">Logout</a>
  </div>

  <div class="hero">
    <img src="./images/logo/logo.png" class="logo" alt="TGuide Logo">
    <h2>TGuide</h2>
  </div>

  <div class="overlay"></div>
  <div class="content">
    <h1>Explore Nepal</h1>
    <p><i>Discover the breathtaking beauty of the Himalayas, ancient culture, and vibrant cities of Nepal.</i></p>
    <div class="btn-box"> 
      <button class="btn" onclick="window.location.href='mainpage.php'">Destinations</button>
      <button class="btn" onclick="window.location.href='gallery.php'">Gallery</button>
    </div>
  </div>
</body>
</html>
