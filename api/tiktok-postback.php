<?php

header('Content-Type: application/json; charset=utf-8');

$subid = $_GET['subid'] ?? '';
$ttclid = $_GET['ttclid'] ?? '';

if ($subid === '') {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'SubID não informado.'
    ]);

    exit;
}

$pixelId = getenv('TIKTOK_PIXEL_ID');
$accessToken = getenv('TIKTOK_ACCESS_TOKEN');

if (!$pixelId || !$accessToken) {
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Variáveis do TikTok não configuradas.'
    ]);

    exit;
}

$payload = [
    'event_source' => 'web',
    'event_source_id' => $pixelId,
    'data' => [
        [
            'event' => 'Purchase',
            'event_time' => time(),

            'user' => [
                'external_id' => hash('sha256', $subid)
            ],

            'page' => [
                'url' => 'https://rosashopstore.vercel.app/'
            ]
        ]
    ]
];

$ch = curl_init(
    'https://business-api.tiktok.com/open_api/v1.3/event/track/'
);

curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Access-Token: ' . $accessToken,
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_TIMEOUT => 15
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

echo json_encode([
    'success' => $httpCode === 200 && $curlError === '',
    'tiktok_http_code' => $httpCode,
    'tiktok_response' => json_decode($response, true),
    'payload_enviado' => $payload,
    'curl_error' => $curlError
]);
