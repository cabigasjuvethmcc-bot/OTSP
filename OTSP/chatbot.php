<?php
// Simple Groq Llama 3 chatbot backend
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data || empty($data['message'])) {
    echo json_encode(['error' => 'No message provided']);
    exit;
}

$userMessage = $data['message'];

$payload = [
    'model' => $GROQ_MODEL,
    'messages' => [
        [
            'role' => 'system',
            'content' => 'You are the AI assistant for OTSP Tech Store, an e-commerce shop for PC parts and electronics. Be concise and helpful, and answer based only on typical PC store knowledge. Do not mention having access to databases or personal data.'
        ],
        [
            'role' => 'user',
            'content' => $userMessage,
        ],
    ],
    'max_tokens' => 256,
];

$ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $GROQ_API_KEY,
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
]);

$response = curl_exec($ch);

if ($response === false) {
    echo json_encode(['error' => 'Request failed']);
    exit;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode >= 400) {
    echo json_encode(['error' => 'Groq API error', 'status' => $httpCode]);
    exit;
}

$decoded = json_decode($response, true);

$reply = $decoded['choices'][0]['message']['content'] ?? 'Sorry, I could not generate a response.';

echo json_encode([
    'reply' => $reply,
]);
