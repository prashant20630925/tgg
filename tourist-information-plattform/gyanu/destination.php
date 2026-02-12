<?php
// Include authentication check - redirects if not logged in
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destinations | TGuide</title>
    <link rel="stylesheet" href="./css/destination.css">
    
    <style>
    /* ===== HEADER STYLES ===== */
    :root {
        --primary-color: #1e73be;
        --primary-dark: #125a94;
        --white: #fff;
        --text-gray: #999;
        --transition: 0.3s ease;
    }

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

    body {
        padding-top: 120px;
        margin: 0;
    }

    header {
        margin-top: 120px;
    }

    .main ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        align-items: center;
    }

    .main ul.list {
        display: flex;
        align-items: center;
        gap: 15px;
        position: static !important;
        float: none !important;
        width: 100%;
        padding: 12px 20px;
        background: transparent;
        margin: 0;
        flex: 1;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 15px;
        flex: 1;
        flex-wrap: nowrap;
    }

    .main .logo a {
        display: flex;
        align-items: center;
        flex-shrink: 0;
    }

    .main .logo img {
        width: 50px;
        height: 50px;
        transition: transform var(--transition);
        position: static !important;
        float: none !important;
        top: auto !important;
        left: auto !important;
        margin: 0 !important;
        animation: none !important;
    }

    .main .logo img:hover {
        transform: scale(1.1) rotate(-5deg);
    }

    .search-box {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1;
        min-width: 200px;
        max-width: 400px;
    }

    .search input[type="text"] {
        flex: 1;
        padding: 10px 14px;
        border: 2px solid var(--white);
        border-radius: 25px;
        font-size: 14px;
        outline: none;
        transition: all var(--transition);
        background: rgba(255, 255, 255, 0.95);
    }

    .search input[type="text"]:focus {
        background: var(--white);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .search input[type="text"]::placeholder {
        color: var(--text-gray);
    }

    .search button,
    .search-box button {
        padding: 10px 14px;
        cursor: pointer;
        border: none;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: var(--white);
        border-radius: 50%;
        font-size: 20px;
        transition: all var(--transition);
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-weight: bold;
    }

    .search button:hover,
    .search-box button:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(30, 115, 190, 0.4);
    }

    .main ul.list2 {
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

    .main ul.list2 li {
        display: inline-block !important;
        float: none !important;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .main ul.list2 li a {
        text-decoration: none;
        color: var(--white);
        padding: 10px 20px;
        display: inline-block;
        font-weight: 600;
        font-size: 15px;
        transition: var(--transition);
        border: 1px solid transparent;
    }

    .main ul.list2 li a:hover {
        background-color: rgba(255, 255, 255, 0.2);
        color: var(--white);
        border-bottom: 2px solid var(--white);
    }

    .main ul.list2 li.active-menu a {
        background-color: rgba(255, 255, 255, 0.3);
        color: var(--white);
        border-bottom: 3px solid var(--white);
    }

    @media (max-width: 768px) {
        body {
            padding-top: 140px;
        }

        .main {
            flex-direction: column;
            gap: 10px;
        }

        .main ul.list {
            flex-wrap: wrap;
            width: 100%;
            flex: auto;
        }

        .search input[type="text"] {
            max-width: 200px;
        }

        .main ul.list2 li a {
            padding: 8px 15px;
            font-size: 13px;
        }

        .main ul.list2 {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        body {
            padding-top: 140px;
        }

        .search input[type="text"] {
            max-width: 150px;
            font-size: 12px;
        }

        .main ul.list2 li a {
            padding: 6px 10px;
            font-size: 11px;
        }

        .search button {
            width: 38px;
            height: 38px;
            font-size: 16px;
        }
    }
    </style>
</head>
<body>
	<header>
        <div class="main">
            <!-- Logo + Search (Same Line) -->
            <ul class="list">
                <li class="logo">
                    <a href="mainPage.php">
                        <img src="./images/logo/logo.png" alt="Logo">
                    </a>
                 
                </li>
            </ul>

            <!-- Menu -->
            <ul class="list2">
                <li><a href="mainPage.php">Home</a></li>
                <li class="active-menu"><a href="destination.php">Destination</a></li>
                <li><a href="search-destinations.php">Search & Filter</a></li>
                <li><a href="my-wishlist.php">My Wishlist</a></li>
                <li><a href="gallery.html">Gallery</a></li>
                <li><a href="feedback.php">Feedback</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
	</header>

	<section class="things-section">
    <div class="things-container">

       
        <div class="things-image">
            <img src="images/destination/pokhara.jpg" alt="Nepal Image">
        </div>

        <div class="things-content">
            <h1>Top Things to Do in Nepal</h1>
        </div>
       

    </div>
</section>


    <section class="destination-section">
        <div class="heading">
            <h2>Explore Destinations</h2>
            <p>Find the best places to visit in Nepal</p>
        </div>
        
		<div class="category">
			<h3 class="cat-title">Popular Cities in Nepal</h3>
			<div class="destination-container">

				<div class="card">
					<div class="imgBox">
						<img src="./images/destination/kathmadu.jpg" alt="kathmandu">
					 </div>
                    <div class="content">
                        <h4>kathmandu</h4>
                        <p>Central Region,Nepal.</p>
                    </div>
                </div>

                <div class="card">
                    <div class="imgBox">
                        <img src="./images/destination/pokhara.jpg" alt="pokhara">
                    </div>
                    <div class="content">
                        <h4>Pokhara</h4>
                        <p>Western Region , Nepal .</p>
                    </div>
                </div>

                <div class="card">
                    <div class="imgBox">
                        <img src="./images/destination/lumbini.jpg" alt="lumbini">
                    </div>
                    <div class="content">
                        <h4>lumbini</h4>
                        <p>Southern Region, Nepal</p>
                    </div>
                </div>

            </div>
        </div>
		  		
		</div>

        <div class="category">
            <h3 class="cat-title">Most Visited Places</h3>

            <div class="destination-container">

                <div class="card">
                    <div class="imgBox">
                        <img src="./images/destination/boudha.jpg" alt="Boudhanath">
                    </div>
                    <div class="content">
                        <h4>Boudhanath Stupa</h4>
                        <p>One of the largest stupas in the world and a UNESCO World Heritage Site.</p>
                    </div>
                </div>

                <div class="card">
                    <div class="imgBox">
                        <img src="./images/destination/pashupati.jpg" alt="Pashupatinath">
                    </div>
                    <div class="content">
                        <h4>Pashupatinath Temple</h4>
                        <p>A sacred Hindu temple located along the Bagmati River.</p>
                    </div>
                </div>

                <div class="card">
                    <div class="imgBox">
                        <img src="./images/destination/fewalake.jpg" alt="Fewa Lake">
                    </div>
                    <div class="content">
                        <h4>Fewa Lake, Pokhara</h4>
                        <p>Popular for boating and the reflection of Machhapuchhre Mountain.</p>
                    </div>
                </div>

            </div>
        </div>


        
        <div class="category">
            <h3 class="cat-title">Other Recommended Places</h3>

            <div class="destination-container">

                <div class="card">
                    <div class="imgBox">
                        <img src="./images/destination/bandipur.jpg" alt="Bandipur">
                    </div>
                    <div class="content">
                        <h4>Bandipur</h4>
                        <p>A beautiful hilltop town with preserved cultural heritage.</p>
                    </div>
                </div>

                <div class="card">
                    <div class="imgBox">
                        <img src="./images/destination/gorkha.jpg" alt="Gorkha">
                    </div>
                    <div class="content">
                        <h4>Gorkha Durbar</h4>
                        <p>Historic palace of King Prithvi Narayan Shah.</p>
                    </div>
                </div>

                <div class="card">
                    <div class="imgBox">
                        <img src="./images/destination/illam.jpg" alt="Illam">
                    </div>
                    <div class="content">
                        <h4>Illam</h4>
                        <p>Famous for tea gardens, lush hills, and peaceful environment.</p>
                    </div>
                </div>

            </div>
        </div>



        <div class="category">
            <h3 class="cat-title">Places for Trekking</h3>

            <div class="destination-container">

                <div class="card">
                    <div class="imgBox">
                        <img src="./images/destination/annapurna.jpg" alt="Annapurna Base Camp">
                    </div>
                    <div class="content">
                        <h4>Annapurna Base Camp</h4>
                        <p>One of Nepal’s most scenic trekking destinations.</p>
                    </div>
                </div>

                <div class="card">
                    <div class="imgBox">
                        <img src="./images/destination/everest.jpg" alt="Everest Base Camp">
                    </div>
                    <div class="content">
                        <h4>Everest Base Camp</h4>
                        <p>A bucket-list trek with breathtaking Himalayan views.</p>
                    </div>
                </div>

                <div class="card">
                    <div class="imgBox">
                        <img src="./images/destination/langtang.jpg" alt="Langtang">
                    </div>
                    <div class="content">
                        <h4>Langtang Valley</h4>
                        <p>Short, beautiful trek close to Kathmandu.</p>
                    </div>
                </div>

            </div>
        </div>

    </section>

</body>
</html>

