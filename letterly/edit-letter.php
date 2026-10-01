<?php
$letterId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$letterId) {
    http_response_code(400);
    exit('A letter id is required.');
}
require __DIR__ . '/create-letter.php';