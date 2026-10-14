<?php
session_start();
include 'connect.php';

// Check if user is logged in and has lecturer role
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: index.php?error=unauthorized");
    exit();
}

$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email=? AND role='lecturer'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0){
    session_destroy();
    header("Location: index.php?error=unauthorized");
    exit();
}

$user = $result->fetch_assoc();
$lecturerId = $user['id'];

$pageTitle = "Manage Courses";
ob_start();

// Fetch courses taught by lecturer
$sqlCourses = "SELECT courses.*, course_offerings.id AS offering_id, course_offerings.semester, course_offerings.year 
                FROM courses 
                JOIN course_offerings ON courses.id = course_offerings.course_id 
                WHERE course_offerings.lecturer_id=?";
$stmt = $conn->prepare($sqlCourses);
$stmt->bind_param("i", $lecturerId);
$stmt->execute();
$coursesResult = $stmt->get_result();

?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Your Courses</h2>
    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
        <thead class="bg-gray-100">
            <tr>
                <th class="py-2 px-4 border-b">Course Code</th>
                <th class="py-2 px-4 border-b">Course Name</th>
                <th class="py-2 px-4 border-b">Semester</th>
                <th class="py-2 px-4 border-b">Year</th>
                <th class="py-2 px-4 border-b">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($course = $coursesResult->fetch_assoc()): ?>
            <tr>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($course['code']); ?></td>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($course['name']); ?></td>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($course['semester']); ?></td>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($course['year']); ?></td>
                <td class="py-2 px-4 border-b">
                    <a href="view_course.php?id=<?php echo $course['offering_id']; ?>" class="text-blue-500 hover:underline">View Details</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>
