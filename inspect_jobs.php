<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pdo = DB::connection()->getPdo();
$columns = DB::connection()->getSchemaBuilder()->getColumnListing('jobs');
print_r($columns);
echo PHP_EOL;

$count = DB::table('jobs')->count();
echo 'jobs_count=' . $count . PHP_EOL;
$rows = DB::table('jobs')->select('id','subcategory','master_category','business_name','latitude','longitude','approved_at','expires_at','status')->limit(10)->get();
foreach ($rows as $row) {
    echo json_encode((array)$row, JSON_UNESCAPED_SLASHES) . PHP_EOL;
}
