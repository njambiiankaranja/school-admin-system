<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=unauthorized");
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM worker_tasks WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: manage_tasks.php");
    exit();
} else {
    echo "Error deleting task.";
}
?>





<?php
$content = ob_get_clean();
include 'template.php';
?>