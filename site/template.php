<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $firstName = htmlspecialchars($user['first_name']);
    $lastName = htmlspecialchars($user['last_name']);
    $email = htmlspecialchars($user['email']);
    $role = htmlspecialchars($user['role']);
    $profileImage = htmlspecialchars($user['profile_image']);
} else {
    echo "User not found.";
    exit();
}

function isActivePage($pageName)
{
    return basename($_SERVER['PHP_SELF']) === $pageName ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .sidebar {
            transition: transform 0.3s ease-in-out;
        }

        .sidebar-hidden {
            transform: translateX(-100%);
        }

        .sidebar-visible {
            transform: translateX(0);
        }
    </style>
</head>

<body class="bg-gray-100 h-screen flex flex-col">
    <div class="flex-1 flex overflow-hidden">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar sidebar-hidden bg-gray-800 text-white w-64 py-6 flex flex-col fixed inset-0 md:relative md:translate-x-0 md:transition-transform md:flex md:flex-col md:w-64 md:py-6 z-20">
            <div class="px-4 py-3 mb-6 flex items-center justify-between md:justify-start">
                <div class="ml-4">
                    <h2 class="text-2xl font-semibold"><?php echo ucfirst($role); ?> Dashboard</h2>
                    <p class="text-sm"><?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></p>
                </div>
                <button id="sidebar-close" class="md:hidden text-gray-400 hover:text-gray-200 focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <nav class="flex-1">
                <!-- <a href="<?php echo $role; ?>dashboard.php" class="flex items-center px-4 py-2 <?php echo isActivePage($role . 'dashboard.php'); ?>">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Dashboard
                </a> -->
                <?php if ($role === 'admin'): ?>
                    <a href="admin.php" class="flex items-center px-4 py-2 <?php echo isActivePage('admin.php'); ?>">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        Dashboard
                    </a>
                    <a href="manage_users.php" class="flex items-center px-4 py-2 <?php echo isActivePage('manage_users.php'); ?>">
                        <i class="fas fa-users mr-3"></i>
                        Users
                    </a>
                    <a href="manage_departments.php" class="flex items-center px-4 py-2 <?php echo isActivePage('manage_departments.php'); ?>">
                        <i class="fas fa-users mr-3"></i>
                        Departments
                    </a>
                    <a href="manage_courses.php" class="flex items-center px-4 py-2 <?php echo isActivePage('manage_courses.php'); ?>">
                        <i class="fas fa-users mr-3"></i>
                        Courses
                    </a>
                    <a href="manage_calendar.php" class="flex items-center px-4 py-2 <?php echo isActivePage('manage_calendar.php'); ?>">
                        <i class="fas fa-users mr-3"></i>
                        Calendar
                    </a>
                    <a href="manage_announcements.php" class="flex items-center px-4 py-2 <?php echo isActivePage('manage_announcements.php'); ?>">
                        <i class="fas fa-users mr-3"></i>
                        Announcements
                    </a>
                    <a href="manage_tasks.php" class="flex items-center px-4 py-2 <?php echo isActivePage('manage_tasks.php'); ?>">
                        <i class="fas fa-users mr-3"></i>
                        Tasks
                    </a>
                <?php endif; ?>
                <?php if ($role === 'student'): ?>
                    <a href="student.php" class="flex items-center px-4 py-2 <?php echo isActivePage('student.php'); ?>">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        Dashboard
                    </a>
                    <a href="my_courses.php" class="flex items-center px-4 py-2 <?php echo isActivePage('my_courses.php'); ?>">
                        <i class="fas fa-book mr-3"></i>
                        Courses
                    </a>
                    <a href="view_schedule.php" class="flex items-center px-4 py-2 <?php echo isActivePage('view_schedule.php'); ?>">
                        <i class="fas fa-book mr-3"></i>
                        Schedule
                    </a>
                    <a href="grades.php" class="flex items-center px-4 py-2 <?php echo isActivePage('grades.php'); ?>">
                        <i class="fas fa-book mr-3"></i>
                        Grades
                    </a>
                    <a href="student_assignments.php" class="flex items-center px-4 py-2 <?php echo isActivePage('student_assignments.php'); ?>">
                        <i class="fas fa-book mr-3"></i>
                        Assignements
                    </a>

                    
                <?php endif; ?>
                <?php if ($role === 'lecturer'): ?>
                    <a href="lecturer.php" class="flex items-center px-4 py-2 <?php echo isActivePage('lecturer.php'); ?>">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Dashboard
                    </a>
                    <a href="assignments.php" class="flex items-center px-4 py-2 <?php echo isActivePage('assignments.php'); ?>">
                        <i class="fas fa-scroll mr-3"></i>
                        Assignments
                    </a>
                    <a href="attendance.php" class="flex items-center px-4 py-2 <?php echo isActivePage('attendance.php'); ?>">
                        <i class="fas fa-tasks mr-3"></i>
                        Attendance
                    </a>
                <?php endif; ?>
                <?php if ($role === 'worker'): ?>
                    <a href="worker.php" class="flex items-center px-4 py-2 <?php echo isActivePage('worker.php'); ?>">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        Dashboard
                    </a>
                    <a href="assigned_tasks.php" class="flex items-center px-4 py-2 <?php echo isActivePage('assigned_tasks.php'); ?>">
                        <i class="fas fa-clipboard-list mr-3"></i>
                        Pending Tasks
                    </a>
                    <a href="in_progress_tasks.php" class="flex items-center px-4 py-2 <?php echo isActivePage('in_progress_tasks.php'); ?>">
                        <i class="fas fa-clipboard-list mr-3"></i>
                        Ongoing Tasks
                    </a>
                    <a href="completed_tasks.php" class="flex items-center px-4 py-2 <?php echo isActivePage('completed_tasks.php'); ?>">
                        <i class="fas fa-clipboard-list mr-3"></i>
                        Completed Tasks
                    </a>
                <?php endif; ?>
                <a href="profile.php" class="flex items-center px-4 py-2 <?php echo isActivePage('profile.php'); ?>">
                    <i class="fas fa-user mr-3"></i>
                    Profile
                </a>
                <a href="announcements.php" class="flex items-center px-4 py-2 <?php echo isActivePage('announcements.php'); ?>">
                    <i class="fas fa-bullhorn mr-3"></i>
                    Announcements
                </a>

            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Navbar -->
            <nav class="bg-white shadow-sm flex items-center justify-between px-4 py-2">
                <div class="flex items-center">
                    <button id="sidebar-toggle" class="text-gray-500 hover:text-gray-700 focus:outline-none md:hidden">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="text-xl font-bold ml-4 md:ml-0"><?php echo $pageTitle; ?></h1>
                </div>
                <div class="flex items-center">
                    <div class="ml-3 relative">
                        <button type="button" class="flex text-sm rounded-full focus:outline-none" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                            <span class="sr-only">Open user menu</span>
                            <?php if ($profileImage): ?>
                                <img src="<?php echo $profileImage; ?>" alt="Profile Image" class="w-full h-full object-cover">
                            <?php else: ?>
                                    <i class="fas fa-user-circle text-2xl"></i>
                            <?php endif; ?>
                        </button>
                        <div class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1" id="user-menu">
                            <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1">Your Profile</a>
                            <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1">Sign out</a>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto py-10 px-4 sm:px-6 lg:px-8">
                <div class="max-w-7xl mx-auto">
                    <?php echo $content; ?>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Toggle sidebar on small screens
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarClose = document.getElementById('sidebar-close');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-hidden');
            sidebar.classList.toggle('sidebar-visible');
        });

        sidebarClose.addEventListener('click', () => {
            sidebar.classList.add('sidebar-hidden');
            sidebar.classList.remove('sidebar-visible');
        });

        // Toggle user menu
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenu = document.getElementById('user-menu');
        userMenuButton.addEventListener('click', () => {
            userMenu.classList.toggle('hidden');
        });
    </script>
</body>

</html>
