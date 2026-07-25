<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Role;
use App\Models\MarketingTarget;
use App\Models\User;
use App\Traits\HasFileUpload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MarketingService extends BaseService
{
    use HasFileUpload;

    public function getAllMarketing(Request $request): LengthAwarePaginator
    {
        $query = User::marketing()->when($request->filled('status'), fn($q) => $q->where('status', $request->input('status')))->orderBy('nama_lengkap');
        return $query->paginate(10)->withQueryString();
    }

    public function createMarketing(array $data): User
    {
        $data['role'] = Role::Marketing->value;
        $data['password'] = Hash::make($data['password']);
        if (($data['foto_profil'] ?? null) instanceof UploadedFile) {
            $data['foto_profil'] = $this->uploadFile($data['foto_profil'], 'foto-profil');
        } elseif (!empty($data['remove_foto_profil'])) {
            $data['foto_profil'] = null;
        } else {
            unset($data['foto_profil']);
        }
        return User::create($data);
    }

    public function updateMarketing(User $marketing, array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        if (($data['foto_profil'] ?? null) instanceof UploadedFile) {
            $data['foto_profil'] = $this->uploadFile($data['foto_profil'], 'foto-profil', $marketing->foto_profil);
        } elseif (!empty($data['remove_foto_profil'])) {
            $this->deleteFile($marketing->foto_profil);
            $data['foto_profil'] = null;
        } else {
            unset($data['foto_profil']);
        }
        $marketing->update($data);
        return $marketing;
    }

    /** @return array<string, float|int|array> */
    public function getKinerja(int $idMarketing, int $bulan, int $tahun): array
    {
        $start = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $prospek = DB::table('prospek')->where('id_marketing', $idMarketing)->whereBetween('tanggal_prospek', [$start->toDateString(), $end->toDateString()])->count();
        $booking = DB::table('booking')->where('id_marketing', $idMarketing)->whereBetween('tanggal_booking', [$start->toDateString(), $end->toDateString()])->count();
        $status = DB::table('status_penjualan')->join('booking', 'booking.id', '=', 'status_penjualan.id_booking')->where('booking.id_marketing', $idMarketing)->whereBetween('status_penjualan.tanggal_perubahan', [$start, $end]);
        $closingRows = (clone $status)->where('status_saat_ini', 'akad')->join('unit_rumah', 'unit_rumah.id', '=', 'status_penjualan.id_unit')->select('unit_rumah.harga_jual', 'booking.id_konsumen', 'unit_rumah.kode_unit', 'status_penjualan.tanggal_perubahan')->get();
        $closing = $closingRows->count();
        $batal = (clone $status)->where('status_saat_ini', 'batal')->count();
        $marketing = User::findOrFail($idMarketing);
        $nilai = (float) $closingRows->sum('harga_jual');
        $komisi = $nilai * ((float) $marketing->persentase_komisi / 100);
        $target = MarketingTarget::where(['id_marketing' => $idMarketing, 'periode_bulan' => $bulan, 'periode_tahun' => $tahun])->first();
        $targetUnit = $target?->target_unit ?? 0;
        return ['prospek' => $prospek, 'booking' => $booking, 'closing' => $closing, 'batal' => $batal, 'conversion_rate' => $prospek ? round($closing / $prospek * 100, 2) : 0, 'total_nilai_penjualan' => $nilai, 'total_komisi' => $komisi, 'target_unit' => $targetUnit, 'pencapaian_target' => $targetUnit ? round($closing / $targetUnit * 100, 2) : 0, 'closing_rows' => $closingRows->all()];
    }

    public function setTarget(array $data): MarketingTarget
    {
        $target = MarketingTarget::updateOrCreate(['id_marketing' => $data['id_marketing'], 'periode_bulan' => $data['periode_bulan'], 'periode_tahun' => $data['periode_tahun']], ['target_unit' => $data['target_unit']]);
        $kinerja = $this->getKinerja((int) $data['id_marketing'], (int) $data['periode_bulan'], (int) $data['periode_tahun']);
        $target->update(['realisasi_unit' => $kinerja['closing'], 'total_nilai_penjualan' => $kinerja['total_nilai_penjualan'], 'total_komisi' => $kinerja['total_komisi']]);
        return $target;
    }

    public function getRanking(int $bulan, int $tahun)
    {
        return $this->getAllMarketing(new Request())->getCollection()->map(fn(User $marketing) => ['marketing' => $marketing, 'kinerja' => $this->getKinerja($marketing->id, $bulan, $tahun)])->sortByDesc(fn($row) => $row['kinerja']['closing'])->values();
    }

    public function getBestMarketing(int $limit = 5)
    {
        $now = now();
        return $this->getRanking($now->month, $now->year)->take($limit);
    }
}
