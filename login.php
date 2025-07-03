<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB config
$host = 'localhost';
$db = 'user_db';
$user = 'root';
$pass = '';

// Connect
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data safely
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Validate
if (empty($email) || empty($password)) {
    die("Please fill in all fields.");
}

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "❌ This email is already registered.";
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
$stmt->bind_param("ss", $email, $hashedPassword);

if ($stmt->execute()) {
    echo "✅ Registration successful!";
    echo "<p>Redirecting to home page in 3 seconds...</p>";

    // Load external redirect JS
    echo '<script src="redirect.js"></script>';

} else {
    echo "❌ Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>