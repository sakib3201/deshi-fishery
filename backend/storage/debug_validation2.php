<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$validator = \Illuminate\Support\Facades\Validator::make(['location' => 'Dhaka'], ['name' => 'required']);
$ex = new \Illuminate\Validation\ValidationException($validator);
echo json_encode($ex->errors());
