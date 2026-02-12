<?php
session_start();

// Block access if admin not logged in
if (!isset($_SESSION['admin']) || empty($_SESSION['admin'])) {
    header('Location: admin-login.php');
    exit;
}

include "admin-config.php";

$id = $_GET['id'];

// fetch existing data
$result = $conn->query("SELECT * FROM destinations WHERE id=$id");
$data = $result->fetch_assoc();

if(isset($_POST['update'])) {

    $name = $_POST['name'];
    $location = $_POST['location'];
    $description = $_POST['description'];

    // check image update
    if(!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        move_uploaded_file($tmp, "../images/destination/".$image);
    } else {
        $image = $data['image'];
    }

    $sql = "UPDATE destinations SET
            name='$name',
            location='$location',
            description='$description',
            image='$image'
            WHERE id=$id";

    if($conn->query($sql)) {
        header("Location: destination-crud.php");
        exit();
    } else {
        echo "Update failed";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Destination</title>

<style>
/* ===== CSS VARIABLES (THEME) ===== */
:root {
    --primary-color: #1e73be;
    --primary-dark: #125a94;
    --success-color: #2ecc71;
    --danger-color: #e74c3c;
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

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, var(--light-bg) 0%, #e8ecf1 100%);
    color: var(--dark-text);
    min-height: 100vh;
    padding: 30px 20px;
}

/* ===== CONTAINER ===== */
.container {
    max-width: 600px;
    margin: 0 auto;
    background: var(--white);
    padding: 40px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
}

h2 {
    color: var(--primary-color);
    margin-bottom: 30px;
    font-size: 28px;
    font-weight: 700;
    letter-spacing: -0.5px;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

/* ===== FORM STYLES ===== */
form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

label {
    font-weight: 600;
    color: var(--dark-text);
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.9;
}

/* ===== INPUT FIELDS ===== */
input[type="text"],
input[type="email"],
input[type="number"],
textarea,
input[type="file"] {
    padding: 14px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 15px;
    font-family: inherit;
    background: #f8f9fa;
    transition: var(--transition);
    color: var(--dark-text);
}

input[type="text"]:focus,
input[type="email"]:focus,
input[type="number"]:focus,
textarea:focus,
input[type="file"]:focus {
    outline: none;
    border-color: var(--primary-color);
    background: var(--white);
    box-shadow: 0 0 0 3px rgba(30, 115, 190, 0.1);
}

input[type="text"]::placeholder,
input[type="email"]::placeholder,
textarea::placeholder {
    color: #999;
}

textarea {
    resize: vertical;
    min-height: 120px;
    line-height: 1.5;
}

/* ===== FILE INPUT STYLING ===== */
input[type="file"] {
    padding: 12px;
    cursor: pointer;
}

input[type="file"]::file-selector-button {
    background: var(--primary-color);
    color: var(--white);
    padding: 8px 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 600;
    transition: var(--transition);
    margin-right: 10px;
}

input[type="file"]::file-selector-button:hover {
    background: var(--primary-dark);
}

/* ===== CURRENT IMAGE DISPLAY ===== */
.current-image {
    margin-top: 10px;
}

.current-image img {
    max-width: 150px;
    max-height: 150px;
    border-radius: 8px;
    box-shadow: var(--shadow-md);
}

/* ===== BUTTONS ===== */
button[type="submit"],
button[type="reset"],
.btn-back {
    padding: 14px 28px;
    font-size: 16px;
    font-weight: 700;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: var(--transition);
    text-decoration: none;
    display: inline-block;
    text-align: center;
    letter-spacing: 0.5px;
}

button[type="submit"] {
    background: linear-gradient(135deg, var(--success-color) 0%, #27ae60 100%);
    color: var(--white);
    box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
}

button[type="submit"]:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(46, 204, 113, 0.4);
}

button[type="submit"]:active {
    transform: translateY(-1px);
}

button[type="reset"] {
    background: #f0f0f0;
    color: var(--dark-text);
    border: 2px solid #ddd;
}

button[type="reset"]:hover {
    background: #e0e0e0;
}

.btn-back {
    background: var(--primary-color);
    color: var(--white);
    margin-top: 10px;
    box-shadow: 0 4px 15px rgba(30, 115, 190, 0.3);
}

.btn-back:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(30, 115, 190, 0.4);
    background: var(--primary-dark);
}

/* ===== BUTTON GROUP ===== */
.button-group {
    display: flex;
    gap: 12px;
    margin-top: 20px;
}

.button-group button {
    flex: 1;
}

/* ===== SUCCESS/ERROR MESSAGES ===== */
.alert {
    padding: 14px 16px;
    border-radius: 6px;
    margin-bottom: 20px;
    font-weight: 600;
    animation: slideDown 0.4s ease-out;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    border-left: 4px solid var(--success-color);
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    border-left: 4px solid var(--danger-color);
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
    .container {
        padding: 30px 20px;
    }

    h2 {
        font-size: 24px;
        margin-bottom: 25px;
    }

    form {
        gap: 16px;
    }

    .button-group {
        flex-direction: column;
    }

    .button-group button {
        width: 100%;
    }
}

@media (max-width: 480px) {
    body {
        padding: 15px 10px;
    }

    .container {
        padding: 20px 15px;
        border-radius: 8px;
    }

    h2 {
        font-size: 20px;
        margin-bottom: 20px;
    }

    input[type="text"],
    input[type="email"],
    textarea,
    input[type="file"] {
        padding: 12px 14px;
        font-size: 16px;
    }

    button[type="submit"],
    button[type="reset"],
    .btn-back {
        padding: 12px 20px;
        font-size: 14px;
    }
}
</style>

</head>
<body>

<div class="container">
    <h2>✏️ Edit Destination</h2>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Destination Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($data['name']); ?>" placeholder="Enter destination name" required>
        </div>

        <div class="form-group">
            <label for="location">Location</label>
            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($data['location']); ?>" placeholder="Enter location" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" placeholder="Enter detailed description" required><?php echo htmlspecialchars($data['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="image">Update Image (Optional)</label>
            <div class="current-image">
                <p>Current Image:</p>
                <img src="../images/destination/<?php echo htmlspecialchars($data['image']); ?>" alt="Current Image">
            </div>
            <input type="file" id="image" name="image" accept="image/*">
        </div>

        <div class="button-group">
            <button type="submit" name="update">✅ Update Destination</button>
            <button type="reset">🔄 Clear Form</button>
        </div>

        <a href="destination-crud.php" class="btn-back">← Back to Destinations</a>
    </form>
</div>

</body>
</html>
