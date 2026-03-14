<?php
session_start();
require_once '../config/db.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'];

// Fetch some basic stats
$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role='student'");
$student_count = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role='teacher'");
$teacher_count = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <!-- Navbar -->
    <nav class="bg-blue-800 text-white p-4 shadow-md flex justify-between items-center">
        <div class="text-xl font-bold tracking-wide">School Admin Panel</div>
        <div class="flex items-center space-x-4">
            <span>Welcome, <?= htmlspecialchars($user_name) ?></span>
            <a href="../auth/logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded text-sm transition">Logout</a>
        </div>
    </nav>

    <div class="container mx-auto p-6 mt-4">
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Widgets -->
            <div class="bg-white p-6 rounded-lg shadow border-t-4 border-blue-500">
                <h3 class="text-gray-500 text-sm font-semibold uppercase">Total Students</h3>
                <p class="text-3xl font-bold mt-2"><?= $student_count ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border-t-4 border-green-500">
                <h3 class="text-gray-500 text-sm font-semibold uppercase">Total Teachers</h3>
                <p class="text-3xl font-bold mt-2"><?= $teacher_count ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border-t-4 border-yellow-500">
                <h3 class="text-gray-500 text-sm font-semibold uppercase">Pending Fees</h3>
                <p class="text-3xl font-bold mt-2">$0.00</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border-t-4 border-purple-500">
                <h3 class="text-gray-500 text-sm font-semibold uppercase">System Alerts</h3>
                <p class="text-3xl font-bold mt-2">0</p>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Admin Modules</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="#" class="block p-4 bg-gray-50 border rounded hover:bg-blue-50 transition">
                    <h3 class="font-semibold text-blue-700">Manage Users</h3>
                    <p class="text-sm text-gray-500">Add or remove teachers, students, and parents.</p>
                </a>
                <a href="#" class="block p-4 bg-gray-50 border rounded hover:bg-blue-50 transition">
                    <h3 class="font-semibold text-blue-700">System Logs</h3>
                    <p class="text-sm text-gray-500">View recent activity and compliance logs.</p>
                </a>
                <a href="#" class="block p-4 bg-gray-50 border rounded hover:bg-blue-50 transition">
                    <h3 class="font-semibold text-blue-700">Financial Reports</h3>
                    <p class="text-sm text-gray-500">Generate fee collection data.</p>
                </a>
            </div>
        </div>

    </div>

</body>
</html>
