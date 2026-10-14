<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=unauthorized");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $stmt = $conn->prepare("INSERT INTO calendar_events (title, description, start_date, end_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $description, $start_date, $end_date);
    if ($stmt->execute()) {
        header("Location: manage_calendar.php");
        exit();
    } else {
        $error = "Error creating event.";
    }
}
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Create New Event</h2>
    <?php if (isset($error)): ?>
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>
    <form action="create_event.php" method="POST">
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" id="title" name="title" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" required>
        </div>
        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea id="description" name="description" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" rows="4" required></textarea>
        </div>
        <div class="mb-4">
            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
            <input type="datetime-local" id="start_date" name="start_date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
        </div>
        <div class="mb-4">
            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
            <input type="datetime-local" id="end_date" name="end_date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
            Create Event
        </button>
    </form>
</div>



<?php
$content = ob_get_clean();
include 'template.php';
?>
