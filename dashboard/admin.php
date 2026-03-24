<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}
$user_name = $_SESSION['name'];

// Fetch REAL Data statistics
$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role='student'");
$student_count = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role='teacher'");
$teacher_count = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT SUM(amount_due - amount_paid) FROM fees WHERE status != 'paid'");
$pending_fees = $stmt->fetchColumn() ?: 0; // Default to 0 if null

$stmt = $pdo->query("SELECT COUNT(*) FROM messages");
$alerts_count = $stmt->fetchColumn();

require_once '../includes/header.php';
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
    <p class="text-gray-500 text-sm mt-1">Overview of school performance and metrics.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Widgets -->
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition">
        <div class="absolute -right-4 -bottom-4 bg-brand-50 rounded-full w-24 h-24 group-hover:scale-110 transition-transform duration-300"></div>
        <div class="relative">
            <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Students</h3>
            <p class="text-3xl font-bold mt-2 text-gray-900"><?= number_format($student_count) ?></p>
            <div class="mt-4 text-xs font-medium text-brand-600 flex items-center">
                <a href="admin_users.php" class="hover:underline">Manage Students &rarr;</a>
            </div>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition">
        <div class="absolute -right-4 -bottom-4 bg-blue-50 rounded-full w-24 h-24 group-hover:scale-110 transition-transform duration-300"></div>
        <div class="relative">
            <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Teachers</h3>
            <p class="text-3xl font-bold mt-2 text-gray-900"><?= number_format($teacher_count) ?></p>
            <div class="mt-4 text-xs font-medium text-blue-600 flex items-center">
                <a href="admin_users.php" class="hover:underline">Manage Teachers &rarr;</a>
            </div>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition">
        <div class="absolute -right-4 -bottom-4 bg-yellow-50 rounded-full w-24 h-24 group-hover:scale-110 transition-transform duration-300"></div>
        <div class="relative">
            <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">Pending Fees</h3>
            <p class="text-3xl font-bold mt-2 text-gray-900">$<?= number_format($pending_fees, 2) ?></p>
            <div class="mt-4 text-xs font-medium text-yellow-600 flex items-center">
                <a href="admin_fees.php" class="hover:underline">Invoice Center &rarr;</a>
            </div>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition">
        <div class="absolute -right-4 -bottom-4 bg-purple-50 rounded-full w-24 h-24 group-hover:scale-110 transition-transform duration-300"></div>
        <div class="relative">
            <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">System Messages</h3>
            <p class="text-3xl font-bold mt-2 text-gray-900"><?= number_format($alerts_count) ?></p>
            <div class="mt-4 text-xs font-medium text-purple-600 flex items-center">
                <a href="messages.php" class="hover:underline">View Inbox &rarr;</a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links / Secondary Modules -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 lg:col-span-2">
        <h2 class="text-lg font-bold mb-4 border-b border-gray-100 pb-2 text-gray-900">Recent User Activity</h2>
        <div class="space-y-4">
            <?php
            // Fetch 3 most recent users as a mock activity feed
            $stmt = $pdo->query("SELECT name, role, created_at FROM users ORDER BY created_at DESC LIMIT 3");
            $recent = $stmt->fetchAll();
            foreach($recent as $r):
            ?>
                <div class="flex items-start">
                    <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-sm flex-shrink-0">
                        <?= strtoupper(substr($r['name'], 0, 1)) ?>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">New <?= htmlspecialchars($r['role']) ?> account created: <?= htmlspecialchars($r['name']) ?></p>
                        <p class="text-xs text-gray-400 mt-1"><?= date('M j, Y, g:i a', strtotime($r['created_at'])) ?></p>
                    </div>
                </div>
            <?php endforeach; 
            if(count($recent) == 0): ?>
                <p class="text-sm text-gray-500 italic">No recent activity.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-lg font-bold mb-4 border-b border-gray-100 pb-2 text-gray-900">Admin Modules</h2>
        <div class="space-y-3">
            <a href="admin_users.php" class="block p-4 bg-gray-50 border border-gray-100 rounded-lg hover:border-brand-300 hover:bg-brand-50 transition group">
                <h3 class="font-semibold text-gray-800 group-hover:text-brand-700">Account Manager</h3>
                <p class="text-xs text-gray-500 mt-1">Add or remove teachers, students, and parents.</p>
            </a>
            <a href="admin_fees.php" class="block p-4 bg-gray-50 border border-gray-100 rounded-lg hover:border-brand-300 hover:bg-brand-50 transition group">
                <h3 class="font-semibold text-gray-800 group-hover:text-brand-700">Financial Reports</h3>
                <p class="text-xs text-gray-500 mt-1">Generate fee collection data and invoices.</p>
            </a>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
