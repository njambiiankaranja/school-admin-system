<?php
session_start();
include 'connect.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=unauthorized");
    exit();
}

// Fetch events
$sql = "SELECT * FROM academic_calendar ORDER BY start_date DESC";
$events = $conn->query($sql);

$pageTitle = "Manage Calendar";
ob_start();
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Manage Calendar Events</h2>

    <?php if (isset($_GET['success'])): ?>
    <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
        <?php echo htmlspecialchars($_GET['success']); ?>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
    <?php endif; ?>

    <a href="create_event.php" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
        Add New Event
    </a>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-4 border-b text-left">Title</th>
                    <th class="py-2 px-4 border-b text-left">Description</th>
                    <th class="py-2 px-4 border-b text-left">Start Date</th>
                    <th class="py-2 px-4 border-b text-left">End Date</th>
                    <th class="py-2 px-4 border-b text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($event = $events->fetch_assoc()): ?>
                <tr>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($event['event_name']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($event['description']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($event['start_date']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($event['end_date']); ?></td>
                    <td class="py-2 px-4 border-b">
                        <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                        |
                        <a href="delete_event.php?id=<?php echo $event['id']; ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure you want to delete this event?')">Delete</a>
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
