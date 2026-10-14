<?php
include 'connect.php';

// validate user credentials
function validateUser($email, $password, $role) {
    global $conn;
    $sql = "SELECT password, role, status FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password']) && $role == $user['role']) {
            // Check if the user account is active
            if ($user['status'] === 'active') {
                return true;
            } else {
                return 'inactive';
            }
        }
    }
    return false;
}

if(isset($_POST['signUp'])){
    $firstName = $_POST['fName'];
    $lastName = $_POST['lName'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    // Check if the email already exists
    $checkSql = "SELECT * FROM users WHERE email = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        header("Location: index.php?error=emailexists");
        exit();
    }

    $sql = "INSERT INTO users (firstName, lastName, email, password, role, status) VALUES (?, ?, ?, ?, ?, 'active')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $firstName, $lastName, $email, $password, $role);
    if($stmt->execute()){
        header("Location: index.php?success=registered");
        exit();
    } else {
        header("Location: index.php?error=registrationfailed");
        exit();
    }
    $stmt->close();
}

if(isset($_POST['signIn'])){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $validationResult = validateUser($email, $password, $role);

    if($validationResult === true){
        session_start();
        $_SESSION['email'] = $email;
        $_SESSION['role'] = $role;
        
        // Redirect based on role
        switch ($role) {
            case 'admin':
                header("Location: admin.php");
                break;
            case 'student':
                header("Location: student.php");
                break;
            case 'lecturer':
                header("Location: lecturer.php");
                break;
            case 'worker':
                header("Location: worker.php");
                break;
            default:
                header("Location: index.php?error=invalidrole");
                break;
        }
        exit();
    } elseif ($validationResult === 'inactive') {
        header("Location: index.php?error=inactiveaccount");
        exit();
    } else {
        header("Location: index.php?error=invalid");
        exit();
    }
}
?>