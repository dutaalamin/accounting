<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

function testRoute($url) {
    global $kernel, $app;
    $request = Illuminate\Http\Request::create($url, 'GET');
    $user = \App\Models\User::first();
    $app['auth']->guard()->login($user);
    $response = $kernel->handle($request);
    echo "$url -> " . $response->getStatusCode() . "\n";
    if ($response->getStatusCode() == 500) {
        if ($response->exception) {
            echo $response->exception->getMessage() . "\n";
            echo $response->exception->getTraceAsString() . "\n";
        } else {
            echo substr($response->getContent(), 0, 500) . "\n";
        }
    }
}

testRoute('/admin/products');
testRoute('/admin/customer-invoices/create');
testRoute('/admin');
