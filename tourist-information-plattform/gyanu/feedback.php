<?php
// Include authentication check - redirects if not logged in
require_once 'auth_check.php';
include "config.php";

$msg = "";
$msg_type = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Verify CSRF token
	$token_valid = false;
	if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token'])) {
		$token_valid = hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
	}
	
	if (!$token_valid) {
		$msg = "Security token validation failed. Please try again.";
		$msg_type = "error";
		$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
	} else {
		$user_name = trim($_POST['name'] ?? '');
		$email = trim($_POST['email'] ?? '');
		$message = trim($_POST['feedbk'] ?? '');

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$msg = "Invalid email address.";
			$msg_type = "error";
		} else {
			$stmt = $conn->prepare("INSERT INTO feedback (user_name, email, message) VALUES (?, ?, ?)");
			if (!$stmt) {
				$msg = "Database error: " . htmlspecialchars($conn->error);
				$msg_type = "error";
			} else {
				$stmt->bind_param('sss', $user_name, $email, $message);
				if ($stmt->execute()) {
					$msg = "Thanks for your feedback! We appreciate your input.";
					$msg_type = "success";
					$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
				} else {
					$msg = "Failed to submit feedback: " . htmlspecialchars($stmt->error);
					$msg_type = "error";
				}
				$stmt->close();
			}
		}
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Feedback Form</title>
	<link rel="stylesheet" type="text/css" href="./css/feedback.css">

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

	.search {
	    display: flex;
	    align-items: center;
	    gap: 8px;
	    flex: 1;
	}

	.search input[type="text"],
	.search-box input[type="text"] {
	    flex: 1;
	    padding: 10px 14px;
	    border: 2px solid var(--white);
	    border-radius: 25px;
	    font-size: 14px;
	    outline: none;
	    transition: all var(--transition);
	    background: rgba(255, 255, 255, 0.95);
	}

	.search input[type="text"]:focus,
	.search-box input[type="text"]:focus {
	    background: var(--white);
	    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
	}

	.search input[type="text"]::placeholder,
	.search-box input[type="text"]::placeholder {
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

	    .search input[type="text"],
	    .search-box input[type="text"] {
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

	    .search input[type="text"],
	    .search-box input[type="text"] {
	        max-width: 150px;
	        font-size: 12px;
	    }

	    .main ul.list2 li a {
	        padding: 6px 10px;
	        font-size: 11px;
	    }

	    .search button,
	    .search-box button {
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
				<li><a href="destination.php">Destination</a></li>
				<li><a href="gallery.html">Gallery</a></li>
				<li class="active-menu"><a href="feedback.php">Feedback</a></li>
				<li><a href="logout.php">Logout</a></li>
			</ul>
		</div>
	</header>
	<div class="breadcrumb" style="margin-bottom: 20px; font-size: 14px;">
		<a href="mainpage.php" style="color: #26137a; text-decoration: none;">Home</a> / Feedback
	</div>
	<div class="feedback">
		<h1>Feedback Form</h1>
		<?php if (!empty($msg)): ?>
			<div class="alert alert-<?php echo $msg_type; ?>" style="padding: 12px; margin: 10px 0; border-radius: 5px; background: <?php echo ($msg_type === 'error' ? '#f8d7da' : '#d4edda'); ?>; color: <?php echo ($msg_type === 'error' ? '#721c24' : '#155724'); ?>;">
				<?php echo htmlspecialchars($msg); ?>
			</div>
		<?php endif; ?>
		<form name='feedbackForm' method="POST" action="feedback.php">
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
			<div class="form-group">
			    <label>Your Name</label>
			    <input type="text" name="name" class="form-control" id="inputText" placeholder="Your Name" required>
			</div>
			<div class="form-group">
			    <label>Your Email</label>
			    <input type="email" name="email" class="form-control" id="inputEmail" placeholder="Your Email" required>
			</div>
			<div class="form-group text1">
			    <label>Feedback:</label>
			    <textarea class="inputTextarea" name="feedbk" rows="4" class="form-control" ng-model='feedback' placeholder="Please write your Feedback here" required></textarea>
			</div>
			<div class="wrapper">
				<button type="submit" class="btn btn-primary" ng-click="performValidation()" name='submit'>Submit Feedback</button>
			</div>
		</form>
	</div>
</body>
</html>