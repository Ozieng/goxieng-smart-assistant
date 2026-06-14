<?php
// ============================================================
// api_get_balance.php
// Returns the current wallet balance for a user.
// Called by Copilot Studio agent (Enterprise Agents track).
// ============================================================

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('db.php');

// === Simple API Key Auth (for Copilot Studio connection) ===
$validApiKey = "GOXIENG_AGENT_KEY_CHANGE_ME"; // Set your own secret key here
$providedKey = $_GET['api_key'] ?? $_POST['api_key'] ?? '';

if ($providedKey !== $validApiKey) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

// === Get user_id ===
$user_id = $_GET['user_id'] ?? $_POST['user_id'] ?? '';

if (empty($user_id) || !is_numeric($user_id)) {
    echo json_encode(['success' => false, 'message' => 'Valid user_id is required.']);
    exit();
}

$user_id = (int) $user_id;

// === Fetch balance ===
$stmt = $conn->prepare("SELECT sms_credits FROM user_balance WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($credits);

if ($stmt->fetch()) {
    $stmt->close();
    echo json_encode([
        'success' => true,
        'user_id' => $user_id,
        'wallet_balance' => (float) $credits,
        'currency' => 'NGN'
    ]);
} else {
    $stmt->close();
    echo json_encode(['success' => false, 'message' => 'User wallet not found.']);
}
?>
