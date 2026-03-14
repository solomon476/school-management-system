<?php
session_start();
require_once '../config/db.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $error = "All fields are required.";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "Email already registered. Try logging in.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashed_password, $role])) {
                $success = "Registration successful! You can now <a href='login.php' class='underline'>log in</a>.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - School Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white p-8 rounded-2xl shadow-lg max-w-md w-full border border-gray-100">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Create an Account</h2>
            <p class="text-gray-500 mt-2 text-sm">Join the School Portal today.</p>
        </div>

        <?php if($error): ?>
            <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-4 text-sm border border-red-100">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if($success): ?>
            <div class="bg-green-50 text-green-700 p-3 rounded-lg mb-4 text-sm border border-green-100">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" placeholder="John Doe">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" placeholder="you@example.com">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">I am a...</label>
                <select name="role" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                    <option value="" disabled selected>Select Role</option>
                    <option value="student">Student</option>
                    <option value="parent">Parent</option>
                    <option value="teacher">Teacher</option>
                </select>
                <p class="text-xs text-gray-400 mt-1">Note: Admin accounts must be created by the system.</p>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-3 px-4 rounded-xl hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition duration-200 mt-2">
                Register Account
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-600">
            Already have an account? <a href="login.php" class="text-blue-600 font-semibold hover:underline">Log in</a>
        </div>
        <div class="mt-2 text-center text-sm text-gray-600">
            <a href="../index.php" class="text-gray-500 hover:text-gray-800 hover:underline">Return to Home</a>
        </div>
    </div>

</body>
</html>
