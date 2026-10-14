<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=unauthorized");
    exit();
}

$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email=? AND role='admin'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0){
    session_destroy();
    header("Location: index.php?error=unauthorized");
    exit();
}

$pageTitle = "Manage Announcements";
ob_start();

// Fetch announcements
$announcements = $conn->query("SELECT * FROM announcements ORDER BY publish_date DESC");

?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Manage Announcements</h2>
    
    <a href="create_announcement.php" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
        Add New Announcement
    </a>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Title</th>
                    <th class="py-2 px-4 border-b">Content</th>
                    <th class="py-2 px-4 border-b">Author</th>
                    <th class="py-2 px-4 border-b">Publish Date</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($announcement = $announcements->fetch_assoc()): ?>
                <tr>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($announcement['title']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($announcement['content']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($announcement['author_id']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($announcement['publish_date']); ?></td>
                    <td class="py-2 px-4 border-b">
                        <a href="edit_announcement.php?id=<?php echo $announcement['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                        |
                        <a href="delete_announcement.php?id=<?php echo $announcement['id']; ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure you want to delete this announcement?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>
