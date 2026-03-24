<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../auth/login.php");
    exit();
}
$user_name = $_SESSION['name'];

// Fetch some overview stats
$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role='student'");
$student_count = $stmt->fetchColumn();

// Count today's absences
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE date = ? AND status = 'absent'");
$stmt->execute([$today]);
$absences_today = $stmt->fetchColumn();

require_once '../includes/header.php';
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Teacher Dashboard</h1>
    <p class="text-gray-500 text-sm mt-1">Manage attendance, grades, and parent communication.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition">
        <div class="absolute -right-4 -bottom-4 bg-brand-50 rounded-full w-24 h-24 group-hover:scale-110 transition-transform duration-300"></div>
        <div class="relative">
            <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">My Students</h3>
            <p class="text-3xl font-bold mt-2 text-gray-900"><?= number_format($student_count) ?></p>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition">
        <div class="absolute -right-4 -bottom-4 bg-red-50 rounded-full w-24 h-24 group-hover:scale-110 transition-transform duration-300"></div>
        <div class="relative">
            <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">Absences Today</h3>
            <p class="text-3xl font-bold mt-2 text-gray-900"><?= number_format($absences_today) ?></p>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition">
        <div class="absolute -right-4 -bottom-4 bg-blue-50 rounded-full w-24 h-24 group-hover:scale-110 transition-transform duration-300"></div>
        <div class="relative">
            <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">Pending Grading</h3>
            <p class="text-3xl font-bold mt-2 text-gray-900">0</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <h2 class="text-lg font-bold mb-4 border-b border-gray-100 pb-2 text-gray-900">Quick Actions</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Attendance Card -->
        <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition hover:border-brand-300 group">
            <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            </div>
            <h3 class="font-bold text-lg text-gray-900">Daily Attendance</h3>
            <p class="text-gray-500 mb-4 text-sm mt-1">Mark student attendance for your classes. Auto-alerts absent students.</p>
            <a href="teacher_attendance.php" class="text-green-700 font-semibold hover:underline flex items-center">
                Take Attendance 
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>

        <!-- Gradebook Card -->
        <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition hover:border-brand-300 group">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <h3 class="font-bold text-lg text-gray-900">Gradebook</h3>
            <p class="text-gray-500 mb-4 text-sm mt-1">Enter assignments, quizzes, and exam scores. Progress auto-calculates.</p>
            <a href="teacher_grades.php" class="text-blue-700 font-semibold hover:underline flex items-center">
                Manage Grades
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>

        <!-- Communication Card -->
        <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition hover:border-brand-300 group md:col-span-2 lg:col-span-1">
            <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
            </div>
            <h3 class="font-bold text-lg text-gray-900">Announcements</h3>
            <p class="text-gray-500 mb-4 text-sm mt-1">Send secure messages directly to parents or students.</p>
            <a href="messages.php" class="text-purple-700 font-semibold hover:underline flex items-center">
                Message Hub
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
