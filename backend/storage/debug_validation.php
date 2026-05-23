<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = \Illuminate\Http\Request::create('/api/v1/farms', 'POST', ['location' => 'Dhaka']);
$request->setUserResolver(fn () => \App\Models\User::factory()->make());

try {
    $form = \App\Http\Requests\Api\V1\StoreFarmRequest::createFrom($request);
    $form->setContainer($app);
    $form->validateResolved();
} catch (\Illuminate\Validation\ValidationException $e) {
    echo json_encode($e->errors());
}
