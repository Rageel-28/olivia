<?php

use App\Models\User;
use App\Models\Aplikasi;
use App\Models\Misi;
use App\Models\MisiAnggota;
use Illuminate\Support\Facades\Hash;

// 1. Buat Developer
$developer = User::firstOrCreate(
    ['email' => 'dev_dummy@playtest.id'],
    [
        'name' => 'Developer Dummy',
        'password' => Hash::make('password'),
        'role' => 'developer',
    ]
);

// 2. Buat Tester
$tester = User::firstOrCreate(
    ['email' => 'tester_dummy@playtest.id'],
    [
        'name' => 'Tester Dummy',
        'password' => Hash::make('password'),
        'role' => 'tester',
    ]
);

// 3. Buat Aplikasi
$aplikasi = Aplikasi::firstOrCreate(
    ['package_name' => 'com.android.chrome'],
    [
        'developer_id' => $developer->id,
        'nama' => 'Google Chrome (Test)',
    ]
);

// 3.5 Buat Paket Dummy
$paket = \App\Models\Paket::first();
if (!$paket) {
    // just in case
    $paket = \App\Models\Paket::create([
        'name' => 'Paket Basic',
        'price' => 0,
        'fee' => 0,
        'short_desc' => 'Basic test',
        'desc' => 'Basic test',
        'features' => json_encode(['A']),
        'points' => 10,
        'capacity' => 10,
        'is_popular' => false,
        'trusted_badge' => false,
    ]);
}

// 4. Buat Misi
$misi = Misi::updateOrCreate(
    [
        'id_user' => $developer->id,
        'nama_aplikasi' => 'Test Auto Tracking Chrome',
    ],
    [
        'id_paket' => $paket->id,
        'tester_id' => $tester->id,
        'aplikasi_id' => $aplikasi->id,
        'status' => 'running', // status running agar muncul di Pantau Progress
        'point' => 50,
        'kapasitas' => 10,
        'logo' => '',
        'link_aplikasi' => 'https://play.google.com/store/apps/details?id=com.android.chrome',
        'instruksi' => 'Buka Chrome selama 3 menit.',
        'total_durasi_detik' => 0,
    ]
);

// 5. Hubungkan Tester ke Misi di MisiAnggota
MisiAnggota::firstOrCreate(
    [
        'id_misi' => $misi->id,
        'id_user' => $tester->id,
    ],
    [
        'status' => 'accepted'
    ]
);

echo "Dummy data created successfully!\n";
echo "Developer: dev_dummy@playtest.id / password\n";
echo "Tester: tester_dummy@playtest.id / password\n";
echo "Aplikasi: com.android.chrome\n";
