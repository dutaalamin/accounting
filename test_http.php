<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/admin/products/create', 'GET');
$user = \App\Models\User::first();
$app['auth']->guard()->login($user);
$response = $kernel->handle($request);
echo $response->getStatusCode() . "\n";
if ($response->getStatusCode() == 500) {
    if ($response->exception) {
        echo $response->exception->getMessage() . "\n";
        echo $response->exception->getTraceAsString();
    } else {
        echo $response->getContent();
    }
}
