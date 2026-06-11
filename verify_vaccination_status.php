<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (App\Models\PetMedicationDetail::with('petDetail:id,name')->get() as $medication) {
    $name = $medication->petDetail->name ?? '?';
    echo "{$name} => {$medication->vaccinationStatusLabel()}\n";
}
