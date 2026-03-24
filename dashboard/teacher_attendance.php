<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../auth/login.php");
    exit();
}

$error = '';
$success = '';
$date_selected = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Handle submit attendance
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'save_attendance') {
    $date = $_POST['attendance_date'];
    $attendance_data = $_POST['status']; // Array of student_id => status

    if (empty($date) || empty($attendance_data)) {
        $error = "Date and attendance data are required.";
    } else {
        $pdo->beginTransaction();
        try {
            // Clear existing records for this date to allow updating
            $stmt = $pdo->prepare("DELETE FROM attendance WHERE date = ?");
            $stmt->execute([$date]);

            // Insert new records
            $stmt = $pdo->prepare("INSERT INTO attendance (student_id, date, status) VALUES (?, ?, ?)");
            foreach ($attendance_data as $student_id => $status) {
                $stmt->execute([$student_id, $date, $status]);
            }
            $pdo->commit();
            $success = "Attendance saved successfully for " . date('M j, Y', strtotime($date));
            $date_selected = $date; // Keep selected date
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to save attendance: " . $e->getMessage();
        }
    }
}

// Fetch all students
$students_stmt = $pdo->query("SELECT id, name FROM users WHERE role = 'student' ORDER BY name ASC");
$students = $students_stmt->fetchAll();

// Fetch existing attendance for selected date
$att_stmt = $pdo->prepare("SELECT student_id, status FROM attendance WHERE date = ?");
$att_stmt->execute([$date_selected]);
$existing_attendance = [];
while ($row = $att_stmt->fetch()) {
    $existing_attendance[$row['student_id']] = $row['status'];
}

require_once '../includes/header.php';
?>

<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Daily Attendance</h1>
        <p class="text-gray-500 text-sm mt-1">Record student presence, absences, and tardiness.</p>
    </div>
    
    <!-- Date Selector -->
    <form class="mt-4 md:mt-0 flex items-center bg-white border border-gray-200 rounded-lg shadow-sm" method="GET">
        <label for="date" class="px-3 text-sm text-gray-500 font-medium">Date:</label>
        <input type="date" name="date" value="<?= htmlspecialchars($date_selected) ?>" class="py-2 pr-3 outline-none text-gray-800 font-medium focus:text-brand-600 bg-transparent" onchange="this.form.submit()">
    </form>
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

<form method="POST" action="">
    <input type="hidden" name="action" value="save_attendance">
    <input type="hidden" name="attendance_date" value="<?= htmlspecialchars($date_selected) ?>">

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 text-gray-600 border-b border-gray-100 uppercase text-xs font-semibold tracking-wider">
                    <tr>
                        <th class="px-6 py-4 w-1/2">Student Name</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach($students as $student): 
                        $status = isset($existing_attendance[$student['id']]) ? $existing_attendance[$student['id']] : 'present'; // default present
                    ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-xs mr-3">
                                        <?= strtoupper(substr($student['name'], 0, 1)) ?>
                                    </div>
                                    <div class="font-medium text-gray-900"><?= htmlspecialchars($student['name']) ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center space-x-2 md:space-x-4">
                                    <label class="inline-flex items-center cursor-pointer group">
                                        <input type="radio" name="status[<?= $student['id'] ?>]" value="present" class="form-radio text-green-500 w-5 h-5 focus:ring-green-500 cursor-pointer" <?= $status == 'present' ? 'checked' : '' ?>>
                                        <span class="ml-2 text-gray-700 group-hover:text-green-700 font-medium">Present</span>
                                    </label>
                                    
                                    <label class="inline-flex items-center cursor-pointer group">
                                        <input type="radio" name="status[<?= $student['id'] ?>]" value="late" class="form-radio text-yellow-500 w-5 h-5 focus:ring-yellow-500 cursor-pointer" <?= $status == 'late' ? 'checked' : '' ?>>
                                        <span class="ml-2 text-gray-700 group-hover:text-yellow-700 font-medium">Late</span>
                                    </label>
                                    
                                    <label class="inline-flex items-center cursor-pointer group">
                                        <input type="radio" name="status[<?= $student['id'] ?>]" value="absent" class="form-radio text-red-500 w-5 h-5 focus:ring-red-500 cursor-pointer" <?= $status == 'absent' ? 'checked' : '' ?>>
                                        <span class="ml-2 text-gray-700 group-hover:text-red-700 font-medium">Absent</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <?php if(count($students) === 0): ?>
                        <tr><td colspan="2" class="px-6 py-8 text-center text-gray-500">No students are enrolled in the system.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if(count($students) > 0): ?>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white px-6 py-2.5 rounded-lg font-medium shadow-sm transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Save Attendance List
                </button>
            </div>
        <?php endif; ?>
    </div>
</form>

<?php require_once '../includes/footer.php'; ?>
