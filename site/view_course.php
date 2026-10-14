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

$courseOfferingId = $_GET['id'];

$pageTitle = "Course Details";
ob_start();

// Fetch course details
$sqlCourse = "SELECT courses.*, course_offerings.* FROM courses 
               JOIN course_offerings ON courses.id = course_offerings.course_id 
               WHERE course_offerings.id=?";
$stmt = $conn->prepare($sqlCourse);
$stmt->bind_param("i", $courseOfferingId);
$stmt->execute();
$courseResult = $stmt->get_result()->fetch_assoc();

// Fetch assignments for the course
$sqlAssignments = "SELECT * FROM assignments WHERE course_offering_id=?";
$stmt = $conn->prepare($sqlAssignments);
$stmt->bind_param("i", $courseOfferingId);
$stmt->execute();
$assignmentsResult = $stmt->get_result();

// Fetch students enrolled in the course
$sqlStudents = "SELECT users.id, CONCAT(users.first_name, ' ', users.last_name) AS name FROM enrollments 
                JOIN users ON enrollments.student_id = users.id 
                WHERE enrollments.course_offering_id=?";
$stmt = $conn->prepare($sqlStudents);
$stmt->bind_param("i", $courseOfferingId);
$stmt->execute();
$studentsResult = $stmt->get_result();
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($courseResult['name']); ?></h2>
    <p class="mb-4"><strong>Course Code:</strong> <?php echo htmlspecialchars($courseResult['code']); ?></p>
    <p class="mb-4"><strong>Semester:</strong> <?php echo htmlspecialchars($courseResult['semester']); ?></p>
    <p class="mb-4"><strong>Year:</strong> <?php echo htmlspecialchars($courseResult['year']); ?></p>
    <p class="mb-4"><strong>Description:</strong> <?php echo htmlspecialchars($courseResult['description']); ?></p>

    <h3 class="text-xl font-bold mt-6 mb-4">Assignments</h3>
    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
        <thead class="bg-gray-100">
            <tr>
                <th class="py-2 px-4 border-b">Title</th>
                <th class="py-2 px-4 border-b">Due Date</th>
                <th class="py-2 px-4 border-b">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($assignment = $assignmentsResult->fetch_assoc()): ?>
            <tr>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($assignment['title']); ?></td>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($assignment['due_date']))); ?></td>
                <td class="py-2 px-4 border-b">
                    <a href="view_assignment.php?id=<?php echo $assignment['id']; ?>" class="text-blue-500 hover:underline">View Details</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h3 class="text-xl font-bold mt-6 mb-4">Enrolled Students</h3>
    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
        <thead class="bg-gray-100">
            <tr>
                <th class="py-2 px-4 border-b">Student ID</th>
                <th class="py-2 px-4 border-b">Name</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($student = $studentsResult->fetch_assoc()): ?>
            <tr>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($student['id']); ?></td>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($student['name']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>
