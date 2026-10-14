<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=unauthorized");
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM announcements WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: manage_announcements.php");
    exit();
} else {
    echo "Error deleting announcement.";
}
?>
