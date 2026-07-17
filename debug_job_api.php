<?php
putenv('APP_ENV=testing');
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=:memory:');
putenv('DB_URL=');

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$kernel->call('migrate:fresh', ['--force' => true]);

App\Models\Job::create([
    'temp_id' => 'temp-1',
    'device_id' => 'device-1',
    'device_os' => 'android',
    'master_category' => 'Services',
    'subcategory' => 'plumbing',
    'business_name' => 'Quick Fix',
    'job_role' => 'Plumber',
    'job_type' => 'full_time',
    'salary' => 1200,
    'phone_number' => '123456789',
    'latitude' => 24.4605,
    'longitude' => 54.3705,
    'city' => 'Abu Dhabi',
    'approved_at' => now(),
    'expires_at' => now()->addDays(2),
    'status' => 'approved',
    'plan_id' => 'plan-1',
]);

$request = Illuminate\Http\Request::create('/api/v1/jobs', 'GET', [
    'latitude' => '24.4600',
    'longitude' => '54.3700',
    'radius' => '5',
    'sub_categories' => 'plumbing,electrical',
    'is_expiry' => 'within_3_days',
]);

$response = $app->make(Illuminate\Contracts\Http\Kernel::class)->handle($request);
echo $response->getStatusCode() . PHP_EOL;
echo $response->getContent() . PHP_EOL;
