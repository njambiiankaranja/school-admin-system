<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=unauthorized");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $target_audience = $_POST['target_audience'];
    $expiry_date = $_POST['expiry_date'];

    $stmt = $conn->prepare("INSERT INTO announcements (title, content, target_audience, expiry_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $content, $target_audience, $expiry_date);
    if ($stmt->execute()) {
        header("Location: manage_announcements.php");
        exit();
    } else {
        $error = "Error creating announcement.";
    }
}
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Create New Announcement</h2>
    <?php if (isset($error)): ?>
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>
    <form action="create_announcement.php" method="POST">
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" id="title" name="title" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" required>
        </div>
        <div class="mb-4">
            <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
            <textarea id="content" name="content" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" rows="4" required></textarea>
        </div>
        <div class="mb-4">
            <label for="target_audience" class="block text-sm font-medium text-gray-700">Target Audience</label>
            <select id="target_audience" name="target_audience" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" required>
                <option value="all">All</option>
                <option value="students">Students</option>
                <option value="lecturers">Lecturers</option>
                <option value="workers">Workers</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="expiry_date" class="block text-sm font-medium text-gray-700">Expiry Date</label>
            <input type="date" id="expiry_date" name="expiry_date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
            Create Announcement
        </button>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>