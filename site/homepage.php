<?php
include 'connect.php';
session_start();

if(!isset($_SESSION['email'])){
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email='$email'";
$result = $conn->query($sql);

if($result->num_rows > 0){
    $row = $result->fetch_assoc();
    $role = $row['role'];
    
    switch ($role) {
        case 'admin':
            header("Location: admin.php");
            break;
        case 'student':
            header("Location: student.php");
            break;
        case 'lecturer':
            header("Location: lecturer.php");
            break;
        case 'worker':
            header("Location: worker.php");
            break;
        default:
            echo "Invalid role.";
            exit();
    }
} else {
    echo "User not found.";
    exit();
}
?>