<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../auth/login.php");
    exit();
}

$error = '';
$success = '';

// Handle add/update grade
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'save_grade') {
    $student_id = $_POST['student_id'];
    $subject = trim($_POST['subject']);
    $score = $_POST['score'];
    $term = trim($_POST['term']);

    if (empty($student_id) || empty($subject) || empty($score) || empty($term)) {
        $error = "All fields are required.";
    } elseif ($score < 0 || $score > 100) {
        $error = "Score must be between 0 and 100.";
    } else {
        // Check if grade for this subject/term already exists to update instead of insert
        $check_stmt = $pdo->prepare("SELECT id FROM grades WHERE student_id = ? AND subject = ? AND term = ?");
        $check_stmt->execute([$student_id, $subject, $term]);
        $existing = $check_stmt->fetchColumn();

        if ($existing) {
            $stmt = $pdo->prepare("UPDATE grades SET score = ? WHERE id = ?");
            if ($stmt->execute([$score, $existing])) {
                $success = "Grade updated successfully!";
            }
        } else {
            $stmt = $pdo->prepare("INSERT INTO grades (student_id, subject, score, term) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$student_id, $subject, $score, $term])) {
                $success = "New grade submitted successfully!";
            }
        }
    }
}

// Fetch all students to populate dropdown
$students_stmt = $pdo->query("SELECT id, name FROM users WHERE role = 'student' ORDER BY name ASC");
$students = $students_stmt->fetchAll();

// Fetch 50 most recent grades
$grades_stmt = $pdo->query("
    SELECT g.id, g.subject, g.score, g.term, g.created_at, u.name as student_name 
    FROM grades g 
    JOIN users u ON g.student_id = u.id 
    ORDER BY g.created_at DESC LIMIT 50
");
$recent_grades = $grades_stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Gradebook Management</h1>
        <p class="text-gray-500 text-sm mt-1">Record class assignments and term results.</p>
    </div>
    <button onclick="document.getElementById('add-modal').classList.remove('hidden')" class="mt-4 md:mt-0 bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition flex items-center">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Enter Grade
    </button>
</div>

<?php if($error): ?>
    <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 border border-red-100"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if($success): ?>
    <div class="bg-green-50 text-green-700 p-4 rounded-lg mb-6 border border-green-100"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Recent Grade Entries</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-white text-gray-500 border-b border-gray-100 uppercase text-xs font-semibold tracking-wider">
                <tr>
                    <th class="px-6 py-4">Student</th>
                    <th class="px-6 py-4">Subject</th>
                    <th class="px-6 py-4">Term/Period</th>
                    <th class="px-6 py-4 text-right">Score</th>
                    <th class="px-6 py-4">Letter Grade</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach($recent_grades as $g): 
                    // Calculate Letter Grade
                    $score = floatval($g['score']);
                    $letter = 'F'; $lcolor = 'text-red-600';
                    if($score >= 90) { $letter = 'A'; $lcolor = 'text-green-600'; }
                    elseif($score >= 80) { $letter = 'B'; $lcolor = 'text-blue-600'; }
                    elseif($score >= 70) { $letter = 'C'; $lcolor = 'text-yellow-600'; }
                    elseif($score >= 60) { $letter = 'D'; $lcolor = 'text-orange-600'; }
                ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            <?= htmlspecialchars($g['student_name']) ?>
                        </td>
                        <td class="px-6 py-4 text-gray-600 uppercase">
                            <?= htmlspecialchars($g['subject']) ?>
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            <?= htmlspecialchars($g['term']) ?>
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-gray-900">
                            <?= number_format($score, 1) ?>%
                        </td>
                        <td class="px-6 py-4 font-bold text-lg <?= $lcolor ?>">
                            <?= $letter ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <?php if(count($recent_grades) === 0): ?>
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No grades have been entered yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Grade Modal -->
<div id="add-modal" class="fixed inset-0 bg-slate-900 bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-900 text-lg">Enter Student Grade</h3>
            <button onclick="document.getElementById('add-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        
        <form action="" method="POST" class="p-6">
            <input type="hidden" name="action" value="save_grade">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Student</label>
                    <select name="student_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 outline-none bg-white">
                        <option value="" disabled selected>-- Choose Student --</option>
                        <?php foreach($students as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject / Class Name</label>
                    <input type="text" name="subject" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 outline-none" placeholder="e.g. Mathematics 101">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Term / Period</label>
                    <input type="text" name="term" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 outline-none" value="Fall 2026">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Score (0-100)</label>
                    <input type="number" step="0.1" min="0" max="100" name="score" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 outline-none" placeholder="85.5">
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('add-modal').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg font-medium transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-medium shadow-sm transition">Save Grade</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
