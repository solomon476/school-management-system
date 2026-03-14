<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
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
    <title>Student Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">

    <nav class="bg-teal-600 text-white p-4 shadow-md flex justify-between items-center">
        <div class="text-xl font-bold tracking-wide">Student Portal</div>
        <div class="flex items-center space-x-4">
            <span class="hidden md:inline">Welcome, <?= htmlspecialchars($user_name) ?></span>
            <a href="../auth/logout.php" class="bg-red-500 hover:bg-red-600 px-3 py-1 md:px-4 md:py-2 rounded text-sm transition">Logout</a>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto p-4 md:p-6 mt-2">
        
        <h1 class="text-2xl font-bold text-gray-800 mb-6">My Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Grades Overview -->
            <div class="bg-white rounded-lg shadow border-t-4 border-teal-500 p-6">
                <div class="flex items-center justify-between mb-4 border-b pb-2">
                    <h2 class="text-xl font-bold text-gray-800">Recent Grades</h2>
                    <a href="#" class="text-teal-600 text-sm hover:underline">View Full Transcript</a>
                </div>
                
                <ul class="space-y-3">
                    <li class="flex justify-between items-center bg-gray-50 p-3 rounded">
                        <span class="font-medium">Mathematics</span>
                        <span class="text-green-600 font-bold">92%</span>
                    </li>
                    <li class="flex justify-between items-center bg-gray-50 p-3 rounded">
                        <span class="font-medium">Science</span>
                        <span class="text-teal-600 font-bold">88%</span>
                    </li>
                    <li class="flex justify-between items-center bg-gray-50 p-3 rounded">
                        <span class="font-medium">History</span>
                        <span class="text-yellow-600 font-bold">81%</span>
                    </li>
                </ul>
            </div>

            <!-- Attendance Overview -->
            <div class="bg-white rounded-lg shadow border-t-4 border-blue-500 p-6">
                <div class="flex items-center justify-between mb-4 border-b pb-2">
                    <h2 class="text-xl font-bold text-gray-800">Attendance Tracker</h2>
                    <a href="#" class="text-blue-600 text-sm hover:underline">View Details</a>
                </div>
                
                <div class="text-center py-6">
                    <div class="relative inline-block">
                        <svg class="w-32 h-32 transform -rotate-90">
                            <circle cx="64" cy="64" r="50" stroke="#f3f4f6" stroke-width="12" fill="none" />
                            <circle cx="64" cy="64" r="50" stroke="#3b82f6" stroke-width="12" fill="none" stroke-dasharray="314" stroke-dashoffset="15" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center flex-col">
                            <span class="text-3xl font-bold text-blue-600">95%</span>
                        </div>
                    </div>
                    <p class="text-gray-500 mt-4 text-sm">You have 2 absences this term.</p>
                </div>
            </div>

            <!-- Quick Homework Links -->
            <div class="bg-white rounded-lg shadow p-6 md:col-span-2">
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Upcoming Assignments</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600">
                                <th class="p-3 font-semibold rounded-tl">Subject</th>
                                <th class="p-3 font-semibold">Assignment</th>
                                <th class="p-3 font-semibold">Due Date</th>
                                <th class="p-3 font-semibold rounded-tr">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b">
                                <td class="p-3">Science</td>
                                <td class="p-3 text-gray-800">Lab Report 3</td>
                                <td class="p-3 text-red-500 font-medium">Tomorrow</td>
                                <td class="p-3"><span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-bold">Pending</span></td>
                            </tr>
                            <tr class="border-b bg-gray-50">
                                <td class="p-3">Math</td>
                                <td class="p-3 text-gray-800">Algebra Worksheet</td>
                                <td class="p-3 text-gray-500">Friday</td>
                                <td class="p-3"><span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-bold">Completed</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</body>
</html>
