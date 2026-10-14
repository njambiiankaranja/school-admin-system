<?php
session_start();
include 'connect.php';

// Check if user is logged in and has worker role
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'worker') {
    header("Location: index.php?error=unauthorized");
    exit();
}

// Get task ID and new status from query parameters
$taskId = $_GET['id'];
$newStatus = $_GET['status'];

if (!in_array($newStatus, ['pending', 'in_progress', 'completed'])) {
    header("Location: worker.php?error=invalid_status");
    exit();
}

// Update the task status
$sql = "UPDATE worker_tasks SET status=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $newStatus, $taskId);
if ($stmt->execute()) {
    // If completed, set completed_date to current date
    if ($newStatus === 'completed') {
        $completedDate = date('Y-m-d');
        $sql = "UPDATE worker_tasks SET completed_date=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $completedDate, $taskId);
        $stmt->execute();
    }
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: worker.php?error=update_failed");
}
?>
