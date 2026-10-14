<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include 'connect.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=unauthorized");
    exit();
}

// Check if department ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_departments.php?error=invalid_id");
    exit();
}

$dept_id = intval($_GET['id']);

// Verify admin status again
$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
if ($stmt === false) {
    die("Prepare failed: " . htmlspecialchars($conn->error));
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    session_destroy();
    header("Location: index.php?error=unauthorized");
    exit();
}

// Check if the department exists
$check_dept = $conn->prepare("SELECT * FROM departments WHERE id = ?");
if ($check_dept === false) {
    die("Prepare failed: " . htmlspecialchars($conn->error));
}
$check_dept->bind_param("i", $dept_id);
$check_dept->execute();
if ($check_dept->get_result()->num_rows === 0) {
    header("Location: manage_departments.php?error=department_not_found");
    exit();
}

// Delete the department
$delete_stmt = $conn->prepare("DELETE FROM departments WHERE id = ?");
if ($delete_stmt === false) {
    die("Prepare failed: " . htmlspecialchars($conn->error));
}
$delete_stmt->bind_param("i", $dept_id);

if ($delete_stmt->execute()) {
    if ($delete_stmt->affected_rows > 0) {
        header("Location: manage_departments.php?success=department_deleted");
    } else {
        echo "Delete query executed but no rows affected. Department ID: " . $dept_id;
        exit();
    }
} else {
    echo "Delete query failed. Error: " . htmlspecialchars($conn->error);
    exit();
}
exit();
?>
