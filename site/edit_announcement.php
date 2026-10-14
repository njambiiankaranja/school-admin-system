<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=unauthorized");
    exit();
}

$id = $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $target_audience = $_POST['target_audience'];
    $expiry_date = $_POST['expiry_date'];

    $stmt = $conn->prepare("UPDATE announcements SET title=?, content=?, target_audience=?, expiry_date=? WHERE id=?");
    $stmt->bind_param("ssssi", $title, $content, $target_audience, $expiry_date, $id);
    if ($stmt->execute()) {
        header("Location: manage_announcements.php");
        exit();
    } else {
        $error = "Error updating announcement.";
    }
} else {
    $stmt = $conn->prepare("SELECT * FROM announcements WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $announcement = $stmt->get_result()->fetch_assoc();
}

?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Edit Announcement</h2>

    <form action="edit_announcement.php?id=<?php echo $id; ?>" method="POST">
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" id="title" name="title" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($announcement['title']); ?>" required>
        </div>
        <div class="mb-4">
            <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
            <textarea id="content" name="content" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" rows="4" required><?php echo htmlspecialchars($announcement['content']); ?></textarea>
        </div>
        <div class="mb-4">
            <label for="target_audience" class="block text-sm font-medium text-gray-700">Target Audience</label>
                        <select id="target_audience" name="target_audience" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" required>
                <option value="all" <?php echo $announcement['target_audience'] === 'all' ? 'selected' : ''; ?>>All</option>
                <option value="students" <?php echo $announcement['target_audience'] === 'students' ? 'selected' : ''; ?>>Students</option>
                <option value="lecturers" <?php echo $announcement['target_audience'] === 'lecturers' ? 'selected' : ''; ?>>Lecturers</option>
                <option value="workers" <?php echo $announcement['target_audience'] === 'workers' ? 'selected' : ''; ?>>Workers</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="expiry_date" class="block text-sm font-medium text-gray-700">Expiry Date</label>
            <input type="date" id="expiry_date" name="expiry_date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($announcement['expiry_date']); ?>">
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
            Update Announcement
        </button>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>
