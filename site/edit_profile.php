



<?php
session_start();
include 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];

// Define allowed file types and max file size (5MB)
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
$maxFileSize = 5 * 1024 * 1024; // 5MB

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    
    // Handle file upload
    $profileImage = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_image']['tmp_name'];
        $fileName = $_FILES['profile_image']['name'];
        $fileSize = $_FILES['profile_image']['size'];
        $fileError = $_FILES['profile_image']['error'];
        
        // Validate file size
        if ($fileSize > $maxFileSize) {
            $error = "File size exceeds the maximum limit of 5MB.";
        } else {
            // Sanitize file name
            $fileName = preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($fileName));
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            
            // Validate file type
            if (in_array($fileExtension, $allowedExtensions)) {
                // Define the upload directory
                $uploadDir = 'uploads/';
                
                // Ensure the upload directory exists
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Generate a unique file name and move the file
                $newFileName = uniqid() . '.' . $fileExtension;
                $destination = $uploadDir . $newFileName;
                
                if (move_uploaded_file($fileTmpPath, $destination)) {
                    $profileImage = $destination;
                } else {
                    $error = "Failed to move uploaded file. Please ensure the 'uploads' directory has the correct permissions.";
                }
            } else {
                $error = "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
            }
        }
    } elseif ($_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $error = "Error uploading file: " . $_FILES['profile_image']['error'];
    }

    // Update user profile
    $sql = "UPDATE users SET first_name = ?, last_name = ?, profile_image = ? WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $profileImage = $profileImage ? $profileImage : NULL;
        $stmt->bind_param("ssss", $firstName, $lastName, $profileImage, $email);
        
        if ($stmt->execute()) {
            $message = "Profile updated successfully.";
        } else {
            $error = "Failed to update profile.";
        }
    } else {
        $error = "Failed to prepare statement.";
    }
}

// Fetch user profile for form pre-fill
$sql = "SELECT * FROM users WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $firstName = htmlspecialchars($user['first_name']);
    $lastName = htmlspecialchars($user['last_name']);
    $profileImage = htmlspecialchars($user['profile_image']);
} else {
    echo "User not found.";
    exit();
}

ob_start();
?>

<div class="bg-white shadow-lg rounded-lg p-8 mx-auto mt-8 max-w-lg">
    <h2 class="text-3xl font-semibold mb-6 text-gray-800">Edit Profile</h2>

    <?php if (isset($message)) { ?>
        <div class="bg-green-100 text-green-800 p-4 rounded-lg mb-4 border border-green-300">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php } ?>

    <?php if (isset($error)) { ?>
        <div class="bg-red-100 text-red-800 p-4 rounded-lg mb-4 border border-red-300">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php } ?>

    <form method="post" enctype="multipart/form-data" class="space-y-6">
        <div>
            <label class="block text-gray-700 text-sm font-medium mb-2" for="first_name">First Name</label>
            <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($firstName); ?>" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div>
            <label class="block text-gray-700 text-sm font-medium mb-2" for="last_name">Last Name</label>
            <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($lastName); ?>" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div>
            <label class="block text-gray-700 text-sm font-medium mb-2" for="profile_image">Profile Image</label>
            <input type="file" name="profile_image" id="profile_image" class="block w-full text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border file:border-gray-300 file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 focus:ring-blue-500 focus:border-blue-500">
            <?php if ($profileImage): ?>
                <div class="mt-4 flex items-center">
                    <img src="<?php echo $profileImage; ?>" alt="Profile Image" class="w-24 h-24 object-cover rounded-full border border-gray-300">
                </div>
            <?php else: ?>
                <div class="mt-4 w-24 h-24 bg-gray-200 rounded-full border border-gray-300 flex items-center justify-center">
                    <span class="text-gray-500">No Image</span>
                </div>
            <?php endif; ?>
        </div>
        <div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Update Profile</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>
