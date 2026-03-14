<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../auth/login.php");
    exit();
}
$user_name = $_SESSION['name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <nav class="bg-green-700 text-white p-4 shadow-md flex justify-between items-center">
        <div class="text-xl font-bold tracking-wide">Teacher Portal</div>
        <div class="flex items-center space-x-4">
            <span>Welcome, <?= htmlspecialchars($user_name) ?></span>
            <a href="../auth/logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded text-sm transition">Logout</a>
        </div>
    </nav>

    <div class="container mx-auto p-6 mt-4">
        
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Attendance Card -->
                <div class="border rounded-lg p-5 hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                    <h3 class="font-bold text-lg">Daily Attendance</h3>
                    <p class="text-gray-500 mb-4 text-sm">Mark student attendance for your classes. Auto-alerts absent students.</p>
                    <a href="#" class="text-green-700 font-semibold hover:underline">Take Attendance &rarr;</a>
                </div>

                <!-- Gradebook Card -->
                <div class="border rounded-lg p-5 hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <h3 class="font-bold text-lg">Gradebook</h3>
                    <p class="text-gray-500 mb-4 text-sm">Enter assignments, quizzes, and exam scores. Progress auto-calculates.</p>
                    <a href="#" class="text-blue-700 font-semibold hover:underline">Manage Grades &rarr;</a>
                </div>

                <!-- Communication Card -->
                <div class="border rounded-lg p-5 hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    </div>
                    <h3 class="font-bold text-lg">Announcements</h3>
                    <p class="text-gray-500 mb-4 text-sm">Send messages to parents or students directly securely.</p>
                    <a href="#" class="text-purple-700 font-semibold hover:underline">Message Hub &rarr;</a>
                </div>

            </div>
        </div>

    </div>

</body>
</html>
