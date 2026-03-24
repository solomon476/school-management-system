<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
$error = '';
$success = '';

// Handle Sending Message
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'send_msg') {
    $receiver_id = $_POST['receiver_id'];
    $subject = trim($_POST['subject']);
    $body = trim($_POST['body']);

    if (empty($receiver_id) || empty($subject) || empty($body)) {
        $error = "All fields are required to send a message.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, subject, body) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$user_id, $receiver_id, $subject, $body])) {
            $success = "Message sent successfully!";
        } else {
            $error = "Failed to send message.";
        }
    }
}

// Fetch Inbox (Messages sent TO this user)
$stmt_inbox = $pdo->prepare("
    SELECT m.id, m.subject, m.body, m.created_at, u.name as sender_name, u.role as sender_role
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE m.receiver_id = ?
    ORDER BY m.created_at DESC
");
$stmt_inbox->execute([$user_id]);
$inbox = $stmt_inbox->fetchAll();

// Fetch Sent Messages (Messages sent BY this user)
$stmt_sent = $pdo->prepare("
    SELECT m.id, m.subject, m.body, m.created_at, u.name as receiver_name, u.role as receiver_role
    FROM messages m
    JOIN users u ON m.receiver_id = u.id
    WHERE m.sender_id = ?
    ORDER BY m.created_at DESC LIMIT 20
");
$stmt_sent->execute([$user_id]);
$sentbox = $stmt_sent->fetchAll();

// Fetch potential receivers (exclude self)
$users_stmt = $pdo->prepare("SELECT id, name, role FROM users WHERE id != ? ORDER BY role, name ASC");
$users_stmt->execute([$user_id]);
$all_users = $users_stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Communication Hub</h1>
        <p class="text-gray-500 text-sm mt-1">Send and receive announcements securely.</p>
    </div>
    <button onclick="document.getElementById('compose-modal').classList.remove('hidden')" class="mt-4 md:mt-0 bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition flex items-center">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
        Compose Message
    </button>
</div>

<?php if($error): ?>
    <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 border border-red-100"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if($success): ?>
    <div class="bg-green-50 text-green-700 p-4 rounded-lg mb-6 border border-green-100"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- INBOX -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-bold text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                Inbox 
                <span class="ml-2 bg-brand-100 text-brand-700 py-0.5 px-2 rounded-full text-xs"><?= count($inbox) ?></span>
            </h3>
        </div>
        <div class="p-0 overflow-y-auto max-h-[600px] divide-y divide-gray-100">
            <?php foreach($inbox as $msg): ?>
                <div class="p-6 hover:bg-gray-50 transition">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-sm mr-3">
                                <?= strtoupper(substr($msg['sender_name'], 0, 1)) ?>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-md"><?= htmlspecialchars($msg['subject']) ?></h4>
                                <p class="text-sm font-medium text-gray-600">From: <?= htmlspecialchars($msg['sender_name']) ?> <span class="text-xs text-gray-400 capitalize">(<?= $msg['sender_role'] ?>)</span></p>
                            </div>
                        </div>
                        <span class="text-xs text-brand-600 font-medium whitespace-nowrap bg-brand-50 px-2 py-1 rounded">
                            <?= date('M j, g:i a', strtotime($msg['created_at'])) ?>
                        </span>
                    </div>
                    <p class="text-gray-700 text-sm mt-3 bg-white border border-gray-100 rounded-lg p-4 shadow-sm whitespace-pre-wrap"><?= htmlspecialchars($msg['body']) ?></p>
                </div>
            <?php endforeach; ?>
            <?php if(count($inbox) === 0): ?>
                <div class="p-10 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    <p class="text-gray-500 font-medium">Your inbox is empty.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- SENT BOX -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-bold text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                Sent Messages
            </h3>
        </div>
        <div class="p-0 overflow-y-auto max-h-[600px] divide-y divide-gray-100">
            <?php foreach($sentbox as $msg): ?>
                <div class="p-5 hover:bg-gray-50 transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-bold text-gray-800 text-sm"><?= htmlspecialchars($msg['subject']) ?></h4>
                        </div>
                        <span class="text-xs text-gray-400 whitespace-nowrap">
                            <?= date('M j', strtotime($msg['created_at'])) ?>
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">To: <?= htmlspecialchars($msg['receiver_name']) ?> <span class="capitalize">(<?= $msg['receiver_role'] ?>)</span></p>
                    <p class="text-gray-600 text-sm mt-2 truncate max-w-sm"><?= htmlspecialchars($msg['body']) ?></p>
                </div>
            <?php endforeach; ?>
            <?php if(count($sentbox) === 0): ?>
                <div class="p-10 text-center">
                    <p class="text-gray-500 text-sm">No sent messages.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Compose Modal -->
<div id="compose-modal" class="fixed inset-0 bg-slate-900 bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-brand-600 text-white">
            <h3 class="font-bold text-lg">New Message</h3>
            <button onclick="document.getElementById('compose-modal').classList.add('hidden')" class="text-brand-200 hover:text-white transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        
        <form action="" method="POST" class="p-6">
            <input type="hidden" name="action" value="send_msg">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To: (Recipient)</label>
                    <select name="receiver_id" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 outline-none bg-white">
                        <option value="" disabled selected>-- Select a Recipient --</option>
                        
                        <?php 
                        $current_role = '';
                        foreach($all_users as $u):
                            if($current_role !== $u['role']) {
                                if($current_role !== '') echo "</optgroup>";
                                $current_role = $u['role'];
                                echo "<optgroup label='" . strtoupper($current_role) . "S'>";
                            }
                        ?>
                            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?></option>
                        <?php endforeach; if($current_role!=='') echo "</optgroup>"; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                    <input type="text" name="subject" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 outline-none" placeholder="Brief subject line">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Message Body</label>
                    <textarea name="body" required rows="5" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 outline-none resize-none" placeholder="Type your message here..."></textarea>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('compose-modal').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg font-medium transition text-sm">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg flex items-center shadow-sm transition font-medium text-sm">
                    Send Message
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
