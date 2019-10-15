<?php
$phoenix_folder = '/mnt/x/Data/www/Projects/Phoenix';
require_once $phoenix_folder . '/vendor/autoload.php';
$app = require_once $phoenix_folder . '/bootstrap/app.php';

$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

$kernel->handle(
  $request = Illuminate\Http\Request::capture()
);

// If you need session
//$id = $app['encrypter']->decrypt($_COOKIE[$app['config']['session.cookie']]);
//$app['session']->driver()->setId($id);
//$app['session']->driver()->start();

return $app;
