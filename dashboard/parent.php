<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
    header("Location: ../auth/login.php");
    exit();
}
$user_name = $_SESSION['name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Parent Portal</title>
    <!-- Tailwind CSS Data -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Extra mobile styling */
        body { -webkit-tap-highlight-color: transparent; }
    </style>
</head>
<body class="bg-gray-50 font-sans">

    <!-- Mobile-First Navbar -->
    <nav class="bg-indigo-600 text-white p-4 shadow-md sticky top-0 z-50">
        <div class="flex justify-between items-center max-w-4xl mx-auto">
            <div class="text-lg font-bold">Parent Portal</div>
            <div class="flex items-center space-x-3">
                <a href="#" class="text-indigo-200 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </a>
                <a href="../auth/logout.php" class="text-sm border border-indigo-400 bg-indigo-700 px-3 py-1 rounded hover:bg-indigo-800 transition">Log Out</a>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto p-4 md:p-6 pb-20">
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Hello, <?= htmlspecialchars($user_name) ?>!</h1>
            <p class="text-gray-500">Here is the latest update for your child.</p>
        </div>

        <div class="space-y-4">
            
            <!-- Child Overview Card -->
            <div class="bg-white rounded-xl shadow p-5 border-l-4 border-indigo-500 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"></path></svg>
                </div>
                <h3 class="font-semibold text-gray-800 text-lg">Student Profile</h3>
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Current Term Grade</p>
                        <p class="font-bold text-xl text-green-600">A-</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Attendance</p>
                        <p class="font-bold text-xl text-blue-600">98%</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <a href="#" class="text-indigo-600 font-medium text-sm flex items-center hover:underline">
                        View Detailed Progress Report 
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>

            <!-- Fees & Forms -->
            <div class="bg-white rounded-xl shadow p-5 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-gray-800 text-lg">Fees & Forms</h3>
                    <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full font-bold">1 Due</span>
                </div>
                <p class="text-sm text-gray-600 mb-4">You have a pending field trip permission slip and activity fee.</p>
                <button class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 rounded-lg transition shadow-sm">
                    Review & Pay Now
                </button>
            </div>

            <!-- Recent Messages -->
            <div class="bg-white rounded-xl shadow p-5">
                <h3 class="font-semibold text-gray-800 text-lg mb-3">Recent Messages</h3>
                <div class="space-y-3">
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <p class="text-xs text-gray-500 mb-1">Today &bull; From Mr. Smith</p>
                        <p class="text-sm text-gray-800">Don't forget the science fair project is due next Tuesday.</p>
                    </div>
                </div>
                <a href="#" class="block text-center text-sm text-indigo-600 mt-4 hover:underline">View All Messages</a>
            </div>

        </div>

    </div>

</body>
</html>
