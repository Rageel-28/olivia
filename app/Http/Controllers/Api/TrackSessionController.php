<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Misi;
use App\Models\MisiAnggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrackSessionController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'tester_id'              => 'required|integer',
            'package_name'           => 'required|string',
            'durasi_tambahan_detik'  => 'required|integer|min:1',
        ]);

        $testerId     = $request->tester_id;
        $packageName  = $request->package_name;
        $durasi       = $request->durasi_tambahan_detik;

        Log::info('TRACK_SESSION_HIT', [
            'tester_id'             => $testerId,
            'package_name'          => $packageName,
            'durasi_tambahan_detik' => $durasi,
        ]);

        // Cari misi yang link_aplikasinya cocok dengan package_name (LIKE match)
        $misi = Misi::where('status', 'running')
            ->where(function ($q) use ($packageName) {
                $q->where('link_aplikasi', $packageName)
                  ->orWhere('link_aplikasi', 'LIKE', '%' . $packageName . '%');
            })
            ->first();

        if (!$misi) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada misi aktif yang cocok untuk package: ' . $packageName,
            ], 404);
        }

        // Cari record MisiAnggota untuk tester ini di misi yang cocok
        $misiAnggota = MisiAnggota::where('id_misi', $misi->id)
            ->where('id_user', $testerId)
            ->first();

        if (!$misiAnggota) {
            return response()->json([
                'success' => false,
                'message' => 'Tester tidak terdaftar di misi ini.',
            ], 403);
        }

        // Tambahkan durasi ke total_durasi_detik milik tester ini
        $misiAnggota->increment('total_durasi_detik', $durasi);

        return response()->json([
            'success'              => true,
            'message'              => 'Durasi berhasil diperbarui.',
            'misi_id'              => $misi->id,
            'tester_id'            => $testerId,
            'total_durasi_detik'   => $misiAnggota->fresh()->total_durasi_detik,
        ]);
    }
}
