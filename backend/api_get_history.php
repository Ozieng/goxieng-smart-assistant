<?php
// ============================================================
// api_get_history.php
// Returns recent message history for a user.
// Called by Copilot Studio agent (Enterprise Agents track).
// ============================================================

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('db.php');

// === Simple API Key Auth ===
$validApiKey = "GOXIENG_AGENT_KEY_CHANGE_ME";
$providedKey = $_GET['api_key'] ?? $_POST['api_key'] ?? '';

if ($providedKey !== $validApiKey) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

// === Get user_id and optional limit ===
$user_id = $_GET['user_id'] ?? $_POST['user_id'] ?? '';
$limit   = $_GET['limit'] ?? $_POST['limit'] ?? 50;

if (empty($user_id) || !is_numeric($user_id)) {
    echo json_encode(['success' => false, 'message' => 'Valid user_id is required.']);
    exit();
}

$user_id = (int) $user_id;
$limit   = min((int) $limit, 200); // cap at 200 for performance

// === Fetch recent messages ===
$stmt = $conn->prepare("SELECT id, phone_number, message, sent_at FROM sms_logs WHERE user_id = ? ORDER BY sent_at DESC LIMIT ?");
$stmt->bind_param("ii", $user_id, $limit);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = [
        'id'           => $row['id'],
        'phone_number' => $row['phone_number'],
        'message'      => $row['message'],
        'sent_at'      => $row['sent_at']
    ];
}
$stmt->close();

echo json_encode([
    'success' => true,
    'user_id' => $user_id,
    'count'   => count($messages),
    'messages' => $messages
]);
?>
