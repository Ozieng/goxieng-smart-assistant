<?php
// ============================================================
// chat_proxy.php
// Sits in /backend/ — receives chat requests from the widget
// and forwards them to Claude API securely from the server.
// This avoids CORS errors from direct browser API calls.
// ============================================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://goxiengbulksms.com.ng');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

$claudeApiKey = "sk-ant-YOUR-KEY-HERE"; 

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['messages']) || !is_array($input['messages'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing messages']);
    exit();
}

$messages     = $input['messages'];
$systemPrompt = $input['system'] ?? '';

// === Forward to Claude API ===
$payload = json_encode([
    'model'      => 'claude-sonnet-4-6',
    'max_tokens' => 1000,
    'system'     => $systemPrompt,
    'messages'   => $messages
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.anthropic.com/v1/messages');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'x-api-key: ' . $claudeApiKey,
    'anthropic-version: 2023-06-01'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response  = curl_exec($ch);
$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    http_response_code(500);
    echo json_encode(['error' => 'Connection error: ' . $curlError]);
    exit();
}

// Return Claude's response directly to the widget
http_response_code($httpCode);
echo $response;
?>
