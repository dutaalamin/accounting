<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/admin/products', 'GET');
$user = clone \App\Models\User::first();

// Properly bind request for auth guard
$app['request'] = $request;

$app['auth']->guard()->login($user);

$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
if ($response->exception) {
    echo "Exception: " . $response->exception->getMessage() . "\n";
    echo $response->exception->getTraceAsString() . "\n";
}
