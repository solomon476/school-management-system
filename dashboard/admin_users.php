<?php
session_start();
require_once '../config/db.php';

// Ensures only admins can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$error = '';
$success = '';

// Handle Delete Request
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    
    // Safety check - don't let admin delete themselves
    if ($id_to_delete != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt->execute([$id_to_delete])) {
            $success = "User deleted successfully.";
        } else {
            $error = "Failed to delete user.";
        }
    } else {
        $error = "You cannot delete your own account.";
    }
}

// Handle Add User Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $error = "All fields are required to add a user.";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "Email already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashed_password, $role])) {
                $success = "User '$name' added successfully!";
            } else {
                $error = "Failed to add user.";
            }
        }
    }
}

// Fetch all users
$stmt = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="mb-6 flex justify-between items-end">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Manage Users</h1>
        <p class="text-gray-500 text-sm mt-1">Add, edit, or remove system access.</p>
    </div>
    <button onclick="document.getElementById('add-modal').classList.remove('hidden')" class="bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition flex items-center">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Add User
    </button>
</div>

<?php if($error): ?>
    <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 border border-red-100 flex items-center">
        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<?php if($success): ?>
    <div class="bg-green-50 text-green-700 p-4 rounded-lg mb-6 border border-green-100 flex items-center">
        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-gray-50 text-gray-600 border-b border-gray-100 uppercase text-xs font-semibold tracking-wider">
                <tr>
                    <th class="px-6 py-4">Name</th>
                    <th class="px-6 py-4">Contact</th>
                    <th class="px-6 py-4">Role</th>
                    <th class="px-6 py-4">Joined</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach($users as $user): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900"><?= htmlspecialchars($user['name']) ?></div>
                            <div class="text-xs text-gray-400">ID: <?= $user['id'] ?></div>
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            <?= htmlspecialchars($user['email']) ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php 
                                $badgeColor = 'bg-gray-100 text-gray-800';
                                if($user['role'] == 'admin') $badgeColor = 'bg-purple-100 text-purple-800';
                                if($user['role'] == 'teacher') $badgeColor = 'bg-blue-100 text-blue-800';
                                if($user['role'] == 'student') $badgeColor = 'bg-green-100 text-green-800';
                                if($user['role'] == 'parent') $badgeColor = 'bg-yellow-100 text-yellow-800';
                            ?>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= $badgeColor ?> capitalize">
                                <?= htmlspecialchars($user['role']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            <?= date('M j, Y', strtotime($user['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <?php if($user['id'] != $_SESSION['user_id']): ?>
                                <a href="?delete=<?= $user['id'] ?>" onclick="return confirm('Are you sure you want to delete <?= htmlspecialchars($user['name']) ?>? This action cannot be undone.')" class="text-red-500 hover:text-red-700 font-medium">Delete</a>
                            <?php else: ?>
                                <span class="text-gray-300 italic">Self</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <?php if(count($users) === 0): ?>
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No users found in database.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add User Modal -->
<div id="add-modal" class="fixed inset-0 bg-slate-900 bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-900 text-lg">Add New User</h3>
            <button onclick="document.getElementById('add-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        
        <form action="" method="POST" class="p-6">
            <input type="hidden" name="action" value="add">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 outline-none bg-white">
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                        <option value="parent">Parent</option>
                        <option value="admin">System Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Initial Password</label>
                    <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 outline-none" value="password123">
                    <p class="text-xs text-gray-500 mt-1">Default: password123</p>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('add-modal').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg font-medium transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-medium shadow-sm transition">Create User</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
