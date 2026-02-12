<?php
session_start();

// Block access if admin not logged in
if (!isset($_SESSION['admin']) || empty($_SESSION['admin'])) {
    header('Location: admin-login.php');
    exit;
}

include "admin-config.php";

$result = $conn->query("SELECT * FROM destinations ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <style>
body{
  font-family:Arial;
  margin:0;
  background:#f4f6f9;
}
.topbar{
  background:#1e73be;
  color:white;
  padding:12px 20px;
  font-size:18px;
}
.sidebar{
  width:220px;
  height:100vh;
  background:#ffffff;
  border-right:1px solid #ddd;
  position:fixed;
  top:48px;
  left:0;
}
.sidebar a{
  display:block;
  padding:12px 15px;
  color:#333;
  text-decoration:none;
  border-bottom:1px solid #eee;
}
.sidebar a:hover{
  background:#1e73be;
  color:white;
}
.content{
  margin-left:240px;
  padding:20px;
}
.card{
  background:white;
  padding:20px;
  border-radius:10px;
  box-shadow:0 0 10px rgba(0,0,0,.1);
  margin-bottom:20px;
}
table{
  width:100%;
  border-collapse:collapse;
  background:white;
}
th,td{
  padding:10px;
  border:1px solid #ddd;
}
th{
  background:#1e73be;
  color:white;
}
.action a{
  margin-right:8px;
}
.btn{
  padding:6px 10px;
  border-radius:5px;
  text-decoration:none;
  color:white;
}
.btn-add{background:#2ecc71;}
.btn-edit{background:#f1c40f;}
.btn-del{background:#e74c3c;}
</style>

<title>Manage Destinations</title>
<?php /* include CSS block */ ?>
</head>

<body>

<div class="topbar">Destination Management</div>

<div class="sidebar">
  <a href="admin-dashboard.php">🏠 Dashboard</a>
  <a href="destination-crud.php">🗺 Manage Destinations</a>
  <a href="users-crud.php">👥 Manage Users</a>
  <a href="admin-logout.php">🚪 Logout</a>
</div>

<div class="content">

<div class="card">
<h2>Destinations</h2>

<a href="add-destination.php" class="btn btn-add">+ Add Destination</a>

<br><br>

<table>
<tr>
<th>ID</th>
<th>Name</th>
<th>Location</th>
<th>Actions</th>
</tr>

<?php while($row=$result->fetch_assoc()): ?>
<tr>
<td><?php echo $row["id"]; ?></td>
<td><?php echo $row["name"]; ?></td>
<td><?php echo $row["location"]; ?></td>

<td class="action">
<a class="btn btn-edit" href="edit-destination.php?id=<?php echo $row["id"]; ?>">Edit</a>
<a class="btn btn-del" href="delete-destination.php?id=<?php echo $row["id"]; ?>">Delete</a>
</td>
</tr>
<?php endwhile; ?>

</table>
</div>

</div>

</body>
</html>

