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
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $stmt = $conn->prepare("UPDATE calendar_events SET title=?, description=?, start_date=?, end_date=? WHERE id=?");
    $stmt->bind_param("ssssi", $title, $description, $start_date, $end_date, $id);
    if ($stmt->execute()) {
        header("Location: manage_calendar.php");
        exit();
    } else {
        $error = "Error updating event.";
    }
}

$stmt = $conn->prepare("SELECT * FROM calendar_events WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Edit Event</h2>
    <?php if (isset($error)): ?>
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>
    <form action="edit_event.php?id=<?php echo $id; ?>" method="POST">
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" id="title" name="title" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($event['title']); ?>" required>
        </div>
        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea id="description" name="description" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" rows="4" required><?php echo htmlspecialchars($event['description']); ?></textarea>
        </div>
        <div class="mb-4">
            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
            <input type="datetime-local" id="start_date" name="start_date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($event['start_date']); ?>">
        </div>
        <div class="mb-4">
            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
            <input type="datetime-local" id="end_date" name="end_date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($event['end_date']); ?>">
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
            Update Event
        </button>
    </form>
</div>



<?php
$content = ob_get_clean();
include 'template.php';
?>
