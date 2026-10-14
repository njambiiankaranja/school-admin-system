<?php
session_start();
include 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch user profile
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

ob_start();
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">User Profile</h2>
    
    <div class="flex items-center mb-6">
        <?php if ($profileImage): ?>
            <img src="<?php echo $profileImage; ?>" alt="Profile Image" class="w-32 h-32 object-cover rounded-full border border-gray-300">
        <?php else: ?>
            <div class="w-32 h-32 bg-gray-200 rounded-full border border-gray-300 flex items-center justify-center">
                <span class="text-gray-500">No Image</span>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <p class="text-gray-600">First Name:</p>
            <p class="font-semibold"><?php echo $firstName; ?></p>
        </div>
        <div>
            <p class="text-gray-600">Last Name:</p>
            <p class="font-semibold"><?php echo $lastName; ?></p>
        </div>
        <div>
            <p class="text-gray-600">Email:</p>
            <p class="font-semibold"><?php echo $email; ?></p>
        </div>
        <div>
            <p class="text-gray-600">Role:</p>
            <p class="font-semibold"><?php echo ucfirst($role); ?></p>
        </div>
    </div>
    
    <div class="mt-6">
        <a href="edit_profile.php" class="bg-blue-500 text-white p-2 rounded text-center hover:bg-blue-600">Edit Profile</a>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>
