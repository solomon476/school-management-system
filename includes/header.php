<?php
// includes/header.php
// Expected to be included after session_start() and db.php
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
$user_name = $_SESSION['name'];
$user_role = $_SESSION['role'];

// Define navigation links based on role
$nav_links = [];
if ($user_role === 'admin') {
    $nav_links = [
        ['url' => 'admin.php', 'icon' => 'home', 'label' => 'Dashboard'],
        ['url' => 'admin_users.php', 'icon' => 'users', 'label' => 'Manage Users'],
        ['url' => 'admin_fees.php', 'icon' => 'currency-dollar', 'label' => 'Financials'],
        ['url' => 'admin_reports.php', 'icon' => 'document-report', 'label' => 'Reports']
    ];
} elseif ($user_role === 'teacher') {
    $nav_links = [
        ['url' => 'teacher.php', 'icon' => 'home', 'label' => 'Dashboard'],
        ['url' => 'teacher_attendance.php', 'icon' => 'check-circle', 'label' => 'Attendance'],
        ['url' => 'teacher_grades.php', 'icon' => 'academic-cap', 'label' => 'Gradebook'],
        ['url' => 'messages.php', 'icon' => 'inbox', 'label' => 'Messages']
    ];
} elseif ($user_role === 'parent') {
    $nav_links = [
        ['url' => 'parent.php', 'icon' => 'home', 'label' => 'Dashboard'],
        ['url' => 'parent_attendance.php', 'icon' => 'calendar', 'label' => 'Attendance'],
        ['url' => 'parent_fees.php', 'icon' => 'credit-card', 'label' => 'Fees & Forms'],
        ['url' => 'messages.php', 'icon' => 'inbox', 'label' => 'Messages']
    ];
} elseif ($user_role === 'student') {
    $nav_links = [
        ['url' => 'student.php', 'icon' => 'home', 'label' => 'Dashboard'],
        ['url' => 'student_transcript.php', 'icon' => 'book-open', 'label' => 'Grades & Transcript']
    ];
}

function getIconSvg($iconName) {
    switch ($iconName) {
        case 'home': return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>';
        case 'users': return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>';
        case 'currency-dollar': return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>';
        case 'document-report': return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>';
        case 'check-circle': return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>';
        case 'academic-cap': return '<path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>';
        case 'inbox': return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>';
        case 'calendar': return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>';
        case 'credit-card': return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>';
        case 'book-open': return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>';
        default: return '';
    }
}

// Get current page to highlight active link
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Portal - <?= ucfirst($user_role) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Tailwind Config for brand colors -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans text-gray-800">

<div class="flex h-screen overflow-hidden">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white flex-shrink-0 hidden md:flex flex-col transition-all duration-300 z-20 overflow-y-auto" id="sidebar">
        <div class="h-16 flex items-center shadow-sm px-6 font-bold text-xl tracking-wide bg-slate-950">
            <span class="text-brand-500 mr-2"><svg class="w-6 h-6 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path></svg></span>
            School Portal
        </div>
        
        <div class="p-6 pb-2">
            <div class="text-xs uppercase text-slate-500 font-semibold tracking-wider mb-2">Navigation</div>
        </div>

        <nav class="flex-1 px-4 space-y-1">
            <?php foreach($nav_links as $link): ?>
                <?php $is_active = ($current_page == $link['url']); ?>
                <a href="<?= $link['url'] ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?= $is_active ? 'bg-brand-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0 <?= $is_active ? 'text-white' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <?= getIconSvg($link['icon']) ?>
                    </svg>
                    <?= $link['label'] ?>
                </a>
            <?php endforeach; ?>
        </nav>
        
        <div class="p-4 border-t border-slate-800">
            <div class="flex items-center">
                <div class="ml-3">
                    <p class="text-sm font-medium text-white"><?= htmlspecialchars($user_name) ?></p>
                    <p class="text-xs font-medium text-slate-400 capitalize"><?= $user_role ?></p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col overflow-hidden relative">
        <!-- Top Navbar -->
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-4 sm:px-6 lg:px-8 z-10">
            <button id="mobile-menu-btn" class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-brand-500 p-2 rounded-md">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <div class="flex-1 px-4 flex justify-between">
                <div class="flex-1 flex items-center lg:hidden font-bold text-slate-800">
                    School Portal
                </div>
                <div class="ml-auto flex items-center space-x-4">
                    <!-- Notifications -->
                    <button class="bg-white p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition">
                        <span class="sr-only">View notifications</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </button>
                    <!-- Settings/Logout -->
                    <div class="relative">
                        <a href="../auth/logout.php" class="text-sm font-medium text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-3 py-2 rounded-md transition-colors border border-red-100">Log Out</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main screen content area -->
        <main class="flex-1 overflow-y-auto bg-gray-50 p-4 sm:p-6 lg:p-8">
            <!-- Mobile Sidebar Overlay -->
            <div id="mobile-overlay" class="fixed inset-0 bg-slate-900 bg-opacity-75 z-20 hidden md:hidden transition-opacity"></div>
            
            <!-- Mobile Sidebar -->
            <div id="mobile-sidebar" class="fixed inset-y-0 left-0 w-64 bg-slate-900 text-white z-30 transform -translate-x-full transition-transform duration-300 md:hidden flex flex-col">
                 <div class="h-16 flex items-center shadow-sm px-6 font-bold text-xl tracking-wide bg-slate-950 justify-between">
                    <span>Menu</span>
                    <button id="close-mobile-menu" class="text-slate-400 hover:text-white">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <nav class="flex-1 px-4 mt-6 space-y-1 overflow-y-auto">
                    <?php foreach($nav_links as $link): ?>
                        <?php $is_active = ($current_page == $link['url']); ?>
                        <a href="<?= $link['url'] ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg <?= $is_active ? 'bg-brand-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                            <svg class="mr-3 h-5 w-5 flex-shrink-0 <?= $is_active ? 'text-white' : 'text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <?= getIconSvg($link['icon']) ?>
                            </svg>
                            <?= $link['label'] ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>

            <!-- Content injected here -->
