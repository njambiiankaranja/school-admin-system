<?php
session_start();
include 'connect.php';

// Check if user is logged in and has student role
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php?error=unauthorized");
    exit();
}

$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email=? AND role='student'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // If no matching student user found, log out and redirect
    session_destroy();
    header("Location: index.php?error=unauthorized");
    exit();
}

$user = $result->fetch_assoc();
$studentId = $user['id'];

$assignmentId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle file submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_FILES['submission_file'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($file["name"]);
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            $sql = "INSERT INTO submissions (student_id, assignment_id, file_path) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iis", $studentId, $assignmentId, $targetFile);
            if ($stmt->execute()) {
                $message = "Assignment submitted successfully!";
            } else {
                $message = "Error submitting assignment.";
            }
        } else {
            $message = "Error uploading file.";
        }
    } else {
        $message = "No file uploaded or file upload error.";
    }
}

// Fetch assignment details
$assignment = $conn->query("
    SELECT a.title, c.name as course_name, a.description
    FROM assignments a
    JOIN course_offerings co ON a.course_offering_id = co.id
    JOIN courses c ON co.course_id = c.id
    WHERE a.id = $assignmentId
")->fetch_assoc();

if (!$assignment) {
    header("Location: student_assignments.php?error=not_found");
    exit();
}

ob_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($assignment['title']); ?></h2>
            <p class="text-sm mb-4">Course: <?php echo htmlspecialchars($assignment['course_name']); ?></p>
            <p class="text-sm mb-4"><?php echo htmlspecialchars($assignment['description']); ?></p>

            <?php if (isset($message)): ?>
                <div class="mb-4 p-4 rounded-lg <?php echo (strpos($message, 'Error') !== false) ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="" method="post" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="submission_file" class="block text-sm font-medium text-gray-700">Upload File</label>
                    <input type="file" id="submission_file" name="submission_file" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <button type="submit" class="bg-blue-500 text-white p-3 rounded-lg shadow hover:bg-blue-600 transition">Submit</button>
            </form>
        </div>
    </div>
</body>

</html>
<?php
$content = ob_get_clean();
include 'template.php';
?>
