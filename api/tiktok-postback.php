<?php

header('Content-Type: application/json; charset=utf-8');

$subid = $_GET['subid'] ?? $_GET['click_id'] ?? $_GET['externalId'] ?? '';

if ($subid === '') {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'SubID não informado.'
    ]);

    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Postback recebido.',
    'subid' => $subid
]);