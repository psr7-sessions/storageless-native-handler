<?php

use Lcobucci\JWT\Builder;
use PSR7Session\Http\SessionMiddleware;
use PSR7SessionsHandler\NativeSessionHandler;

require __DIR__ . '/../vendor/autoload.php';

// 1. Configure the generation of a new token
$config = new Builder();
$config
    ->setIssuer('http://localhost:8080')
    ->setAudience('http://localhost:8080')
    ->setId('4f1g23a12aa', true)
    ->setIssuedAt(time())
    ->setExpiration(time() + 3600)
    ->set('counter', 1)
    ->sign(new Lcobucci\JWT\Signer\Hmac\Sha256(), 'a');

// 2. Check if token already exists
if (! isset($_COOKIE[SessionMiddleware::DEFAULT_COOKIE])) {
    $token = $config->getToken();
} else {
    $parser = new \Lcobucci\JWT\Parser();
    $token  = $parser->parse($_COOKIE[SessionMiddleware::DEFAULT_COOKIE]);
}

// 3. Change session handler
session_set_save_handler(new NativeSessionHandler($config, $token));
session_start();

// 4. Use your sessions as if nothing was changed
$_SESSION['counter'] = isset($_SESSION['counter']) ? $_SESSION['counter'] + 1 : 0;

echo 'Counter ' . $_SESSION['counter'];
