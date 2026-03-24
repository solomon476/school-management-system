<?php
session_start();
require_once '../config/db.php';

// Ensures only admins can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$error = '';
$success = '';

// Add Fee Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add_fee') {
    $student_id = $_POST['student_id'];
    $amount_due = trim($_POST['amount_due']);
    $due_date = $_POST['due_date'];

    if (empty($student_id) || empty($amount_due) || empty($due_date)) {
        $error = "Student, Amount, and Due Date are required.";
    } elseif (!is_numeric($amount_due) || $amount_due <= 0) {
        $error = "Amount must be a valid positive number.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO fees (student_id, amount_due, due_date) VALUES (?, ?, ?)");
        if ($stmt->execute([$student_id, $amount_due, $due_date])) {
            $success = "Fee invoice created successfully!";
        } else {
            $error = "Failed to create fee.";
        }
    }
}

// Mark Fee Paid Request
if (isset($_GET['pay_id']) && is_numeric($_GET['pay_id'])) {
    $pay_id = $_GET['pay_id'];
    // Fast path: mark the paid amount equal to due amount, status = paid.
    $stmt = $pdo->prepare("UPDATE fees SET amount_paid = amount_due, status = 'paid' WHERE id = ?");
    if ($stmt->execute([$pay_id])) {
        $success = "Invoice marked as Paid!";
    }
}

// Fetch all students to populate dropdown
$students_stmt = $pdo->query("SELECT id, name FROM users WHERE role = 'student' ORDER BY name ASC");
$students = $students_stmt->fetchAll();

// Fetch all fees with student details
$fees_stmt = $pdo->query("
    SELECT f.id, f.amount_due, f.amount_paid, f.status, f.due_date, u.name as student_name 
    FROM fees f 
    JOIN users u ON f.student_id = u.id 
    ORDER BY f.due_date DESC
");
$fees = $fees_stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Financial Reports</h1>
        <p class="text-gray-500 text-sm mt-1">Manage student invoicing and fee collection.</p>
    </div>
    <button onclick="document.getElementById('add-fee-modal').classList.remove('hidden')" class="mt-4 md:mt-0 bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition flex items-center">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        New Invoice
    </button>
</div>

<?php if($error): ?>
    <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 border border-red-100"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if($success): ?>
    <div class="bg-green-50 text-green-700 p-4 rounded-lg mb-6 border border-green-100"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<!-- System Summary -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <?php
        $total_due = 0;
        $total_paid = 0;
        foreach($fees as $f) {
            $total_due += $f['amount_due'];
            $total_paid += $f['amount_paid'];
        }
    ?>
    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Billed</p>
        <p class="text-2xl font-bold text-gray-900 mt-2">$<?= number_format($total_due, 2) ?></p>
    </div>
    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm border-l-4 border-l-green-500">
        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Collected</p>
        <p class="text-2xl font-bold text-green-600 mt-2">$<?= number_format($total_paid, 2) ?></p>
    </div>
    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm border-l-4 border-l-red-500">
        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Outstanding Balance</p>
        <p class="text-2xl font-bold text-red-600 mt-2">$<?= number_format($total_due - $total_paid, 2) ?></p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
        <h3 class="font-bold text-gray-800">Fee Registry</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-white text-gray-500 border-b border-gray-100 uppercase text-xs font-semibold tracking-wider">
                <tr>
                    <th class="px-6 py-4">Student</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Due Date</th>
                    <th class="px-6 py-4 text-right">Amount Billed</th>
                    <th class="px-6 py-4 text-right">Balance Due</th>
                    <th class="px-6 py-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach($fees as $fee): 
                    $balance = $fee['amount_due'] - $fee['amount_paid'];
                    $is_overdue = ($fee['status'] != 'paid' && strtotime($fee['due_date']) < time());
                ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            <?= htmlspecialchars($fee['student_name']) ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($fee['status'] == 'paid'): ?>
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">Paid</span>
                            <?php elseif($is_overdue): ?>
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">Overdue</span>
                            <?php else: ?>
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-gray-500 <?= $is_overdue ? 'text-red-500 font-medium' : '' ?>">
                            <?= date('M j, Y', strtotime($fee['due_date'])) ?>
                        </td>
                        <td class="px-6 py-4 text-right font-medium text-gray-900">
                            $<?= number_format($fee['amount_due'], 2) ?>
                        </td>
                        <td class="px-6 py-4 text-right font-bold <?= $balance > 0 ? 'text-red-600' : 'text-green-600' ?>">
                            $<?= number_format($balance, 2) ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if($balance > 0): ?>
                                <a href="?pay_id=<?= $fee['id'] ?>" onclick="return confirm('Mark full balance of $<?= number_format($balance, 2) ?> as PAID?')" class="text-xs bg-brand-100 text-brand-700 hover:bg-brand-600 hover:text-white px-3 py-1.5 rounded transition font-bold border border-brand-200">
                                    Mark Paid
                                </a>
                            <?php else: ?>
                                <span class="text-xs text-gray-400 italic">No Action</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <?php if(count($fees) === 0): ?>
                    <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No invoices have been issued.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Fee Modal -->
<div id="add-fee-modal" class="fixed inset-0 bg-slate-900 bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-900 text-lg">Create New Invoice</h3>
            <button onclick="document.getElementById('add-fee-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        
        <form action="" method="POST" class="p-6">
            <input type="hidden" name="action" value="add_fee">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Student</label>
                    <select name="student_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 outline-none bg-white">
                        <option value="" disabled selected>-- Choose Student --</option>
                        <?php foreach($students as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                        <?php endforeach; ?>
                        <?php if(count($students)==0): ?>
                            <option value="" disabled>No students exist yet.</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount Due ($)</label>
                    <input type="number" step="0.01" min="0" name="amount_due" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 outline-none" placeholder="e.g. 150.00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                    <input type="date" name="due_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 outline-none" value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('add-fee-modal').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg font-medium transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-medium shadow-sm transition">Generate Invoice</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
