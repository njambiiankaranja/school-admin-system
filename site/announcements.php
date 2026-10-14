<?php
session_start();
include 'connect.php';

// Fetch all announcements
$sql = "SELECT a.*, u.first_name, u.last_name FROM announcements a LEFT JOIN users u ON a.author_id = u.id ORDER BY a.publish_date DESC";
$result = $conn->query($sql);

$pageTitle = "Announcements";
ob_start();
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Announcements</h2>
    
    <?php if ($result->num_rows > 0): ?>
        <div class="space-y-4">
            <?php while ($announcement = $result->fetch_assoc()): ?>
                <div class="bg-gray-100 p-4 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($announcement['title']); ?></h3>
                    <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($announcement['content']); ?></p>
                    <p class="text-sm text-gray-500">
                        Published by <?php echo htmlspecialchars($announcement['first_name'] . ' ' . $announcement['last_name']); ?>
                        on <?php echo htmlspecialchars($announcement['publish_date']); ?>
                    </p>
                    <?php if ($announcement['expiry_date'] && $announcement['expiry_date'] < date('Y-m-d')): ?>
                        <p class="text-sm text-red-500">This announcement has expired.</p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-600">No announcements available at the moment.</p>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>
