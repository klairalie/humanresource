<?php
require __DIR__ . '/../bootstrap/app.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(App\Http\Kernel::class);
echo "Kernel aliases:\n";
var_export($kernel->getMiddlewareAliases());
echo "\nRouter middleware:\n";
var_export($app->make(Illuminate\Routing\Router::class)->getMiddleware());
