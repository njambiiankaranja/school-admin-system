<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=unauthorized");
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM calendar_events WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: manage_calendar.php");
    exit();
} else {
    echo "Error deleting event.";
}
?>
