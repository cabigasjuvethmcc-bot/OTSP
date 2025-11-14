<?php
require_once __DIR__ . '/../config.php';

$apiKey = isset($GROQ_API_KEY) ? $GROQ_API_KEY : '';

$input = json_decode(file_get_contents("php://input"), true);
$message = isset($input["message"]) ? trim($input["message"]) : '';

header('Content-Type: application/json');

// Basic validation
if ($message === '') {
    echo json_encode([
        'reply' => 'Hi! Ask me anything about OTSP Tech Store products, pricing, or orders and I\'ll help. 😊'
    ]);
    exit;
}

// API key check (helps during setup)
if ($apiKey === 'YOUR_GROQ_API_KEY' || $apiKey === '') {
    echo json_encode([
        'reply' => 'The assistant is almost ready, but the Groq API key is not configured yet. Please ask your developer to set it in backend/chat.php.'
    ]);
    exit;
}

$payload = [
    'model' => isset($GROQ_MODEL) ? $GROQ_MODEL : 'llama3-8b-8192',
    'messages' => [
        [
            'role' => 'system',
            'content' => 'You are OTSP Assistant, a friendly expert for the OTSP Tech Store website. ' .
                'You help customers with laptops, desktops, PC components, and peripherals. ' .
                'Be conversational, concise, and specific. Use the tone of a knowledgeable store staff member. ' .
                'If the user asks about something not sold in a typical PC store, politely say you may not have that item but you can still offer general tech advice.'
        ],
        [
            'role' => 'user',
            'content' => $message,
        ],
    ],
];

$ch = curl_init("https://api.groq.com/openai/v1/chat/completions");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $apiKey"
]);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if ($response === false) {
    echo json_encode([
        'reply' => 'I\'m having trouble reaching the AI service right now. Please check your internet connection or try again in a moment.'
    ]);
    curl_close($ch);
    exit;
}

curl_close($ch);

// Decode Groq response and wrap into { "reply": "..." }
$decoded = json_decode($response, true);

if (!is_array($decoded)) {
    echo json_encode([
        'reply' => 'The AI service returned an unexpected response format. Please try again later.'
    ]);
    exit;
}

// If Groq returned an error object, show that message so it can be fixed.
if (isset($decoded['error']['message'])) {
    echo json_encode([
        'reply' => 'AI service error: ' . $decoded['error']['message']
    ]);
    exit;
}

$reply = isset($decoded['choices'][0]['message']['content'])
    ? $decoded['choices'][0]['message']['content']
    : 'Sorry, I could not generate a response just now, but you can try rephrasing your question.';

echo json_encode(['reply' => $reply]);
?>
