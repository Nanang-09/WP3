<?php
$s1 = App\Models\Service::where('name', 'Pembangunan Rumah')->first();
if($s1) {
    $s1->price_start = 850000;
    $s1->price_unit = 'per meter';
    $s1->save();
}

$s2 = App\Models\Service::where('name', 'Konstruksi Baja & Las')->first();
if($s2) {
    $s2->price_start = 25000;
    $s2->price_unit = 'per kg';
    $s2->save();
}
