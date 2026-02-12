<?php
$conn = new mysqli("localhost","root","","tguidee");

if($conn->connect_error){
    die("Admin DB Failed");
}
?>
