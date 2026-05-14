<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$jobs = App\Models\JobPosting::with('user')->where('status', 'active')->latest()->get();
echo json_encode($jobs->toArray(), JSON_PRETTY_PRINT);
