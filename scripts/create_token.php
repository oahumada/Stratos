<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = $argv[1] ?? 'admin@example.com';
$user = \App\Models\User::where('email', $email)->first();
if (! $user) {
    echo "NOUSER\n";
    exit(1);
}
$token = $user->createToken('rcm-cli');
echo $token->plainTextToken."\n";
