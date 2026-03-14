<?php
session_start();

// Redirect based on role if already logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    header("Location: dashboard/" . $_SESSION['role'] . ".php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System</title>
    <!-- Tailwind CSS Data -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-gray-50 font-sans text-gray-800 flex items-center justify-center min-h-screen">

    <div class="bg-white p-10 rounded-2xl shadow-xl max-w-md w-full text-center border border-gray-100">
        <!-- Logo Icon -->
        <div class="mx-auto w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-6">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
            </svg>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-3 tracking-tight">School Portal</h1>
        <p class="text-gray-500 mb-8 max-w-sm mx-auto">Access your personalized dashboard to manage attendance, grades, and communications.</p>
        
        <div class="space-y-4">
            <a href="auth/login.php" class="block w-full bg-blue-600 text-white font-semibold py-3 px-4 rounded-xl shadow hover:bg-blue-700 hover:shadow-lg transition duration-200">
                Sign In
            </a>
            <a href="auth/register.php" class="block w-full bg-gray-50 text-gray-700 font-semibold py-3 px-4 rounded-xl border border-gray-200 hover:bg-gray-100 transition duration-200">
                Create Account
            </a>
        </div>
        
        <div class="mt-8 pt-6 border-t border-gray-100 text-xs text-gray-400">
            <p>Secure &bull; Quick &bull; Mobile-Ready</p>
        </div>
    </div>

</body>
</html>
