<?php
// ============================================================
// api_suggest_templates.php
// THE "SMART" ENDPOINT — analyzes a user's past sms_logs and
// groups/suggests reusable message templates by category.
// This is the "grounded intelligence over enterprise data"
// feature for the Copilot Studio agent.
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

// === Get user_id ===
$user_id = $_GET['user_id'] ?? $_POST['user_id'] ?? '';

if (empty($user_id) || !is_numeric($user_id)) {
    echo json_encode(['success' => false, 'message' => 'Valid user_id is required.']);
    exit();
}

$user_id = (int) $user_id;

// === Fetch last 300 messages for analysis ===
$stmt = $conn->prepare("SELECT message, sent_at FROM sms_logs WHERE user_id = ? AND message IS NOT NULL AND message != '' ORDER BY sent_at DESC LIMIT 300");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    // Skip voice message entries (logged as [VOICE] url)
    if (strpos($row['message'], '[VOICE]') === 0) continue;
    $messages[] = $row['message'];
}
$stmt->close();

if (empty($messages)) {
    echo json_encode([
        'success' => true,
        'user_id' => $user_id,
        'categories' => [],
        'message' => 'No text message history found to analyze yet.'
    ]);
    exit();
}

// === Categorization Rules ===
// Keyword-based classification — lightweight, fast, explainable.
// Each category has keywords that trigger a match.
$categoryKeywords = [
    'Loan Reminder' => ['loan', 'repay', 'repayment', 'due', 'overdue', 'outstanding', 'balance', 'payment', 'defaul', 'owe', 'debt', 'installment'],
    'Church / Religious' => ['church', 'service', 'sunday', 'fellowship', 'prayer', 'crusade', 'pastor', 'congregation', 'worship', 'bible study'],
    'Meeting / Event' => ['meeting', 'event', 'agenda', 'venue', 'attend', 'schedule', 'rescheduled', 'invite', 'invitation', 'conference'],
    'Promotional / Marketing' => ['discount', 'offer', 'promo', 'sale', 'free', 'win', 'prize', 'bonus', 'limited time'],
    'OTP / Verification' => ['otp', 'verification', 'code', 'pin', 'verify', 'confirm your'],
    'Birthday / Greetings' => ['birthday', 'happy', 'congratulations', 'celebrate', 'anniversary'],
];

// === Group messages into categories ===
$categorized = [];
$uncategorized = [];

foreach ($messages as $msg) {
    $lowerMsg = strtolower($msg);
    $matchedCategory = null;

    foreach ($categoryKeywords as $category => $keywords) {
        foreach ($keywords as $keyword) {
            if (strpos($lowerMsg, $keyword) !== false) {
                $matchedCategory = $category;
                break 2;
            }
        }
    }

    if ($matchedCategory) {
        $categorized[$matchedCategory][] = $msg;
    } else {
        $uncategorized[] = $msg;
    }
}

// === Build template suggestions per category ===
// For each category, pick the most recent message as the "template"
// and generalize it by replacing common variable patterns.
function generalizeTemplate($message) {
    $template = $message;

    // Replace common variable patterns with placeholders
    $template = preg_replace('/\b\d{4,}\b/', '{AMOUNT}', $template);          // numbers 4+ digits -> amount/code
    $template = preg_replace('/\b\d{1,2}\/\d{1,2}\/\d{2,4}\b/', '{DATE}', $template); // dates
    $template = preg_replace('/\b\d{1,2}(am|pm|AM|PM)\b/', '{TIME}', $template); // times
    $template = preg_replace('/₦\s?[\d,]+/', '₦{AMOUNT}', $template);          // Naira amounts

    return $template;
}

$suggestions = [];

foreach ($categorized as $category => $msgs) {
    $count = count($msgs);

    // Use the most recent message as the base template
    $sampleMessage = $msgs[0];
    $suggestedTemplate = generalizeTemplate($sampleMessage);

    $suggestions[] = [
        'category'          => $category,
        'message_count'     => $count,
        'frequency_label'   => $count >= 10 ? 'Frequently used' : ($count >= 3 ? 'Occasionally used' : 'Used a few times'),
        'sample_message'    => $sampleMessage,
        'suggested_template'=> $suggestedTemplate,
        'recommendation'    => "You've sent $count message(s) in this category. Consider saving this as a reusable template to speed up future sends."
    ];
}

// Sort by message_count descending — most-used categories first
usort($suggestions, function($a, $b) {
    return $b['message_count'] - $a['message_count'];
});

echo json_encode([
    'success' => true,
    'user_id' => $user_id,
    'total_messages_analyzed' => count($messages),
    'categories_found' => count($suggestions),
    'uncategorized_count' => count($uncategorized),
    'suggestions' => $suggestions
]);
?>
