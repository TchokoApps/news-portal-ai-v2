<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use App\Models\Admin;

$admin = Admin::where('email', 'pwtest@local.test')->first();
if ($admin) {
    echo "Token: " . $admin->remember_token . "\n";
} else {
    echo "Admin not found\n";
}
