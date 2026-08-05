<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MetodePembayaran;
use App\Models\SimulasiPembayaran;
use App\Models\UnitRumah;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;

final class SimulasiService
{
    public function hitungKpr(
        float $hargaRumah,
        float $dpPersen,
        int $tenorTahun,
        float $sukuBungaPersen
    ): array {
        $dpNominal = $hargaRumah * ($dpPersen / 100);
        $plafon = $hargaRumah - $dpNominal;

        if ($plafon <= 0) {
            throw new InvalidArgumentException('DP terlalu besar. Plafon KPR harus lebih dari 0.');
        }

        $bungaBulanan = $sukuBungaPersen / 100 / 12;
        $jumlahBulan = $tenorTahun * 12;

        if ($jumlahBulan <= 0) {
            throw new InvalidArgumentException('Tenor harus lebih dari 0.');
        }

        if ($bungaBulanan === 0.0) {
            $cicilanBulanan = $plafon / $jumlahBulan;
        } else {
            $cicilanBulanan = $plafon *
                ($bungaBulanan * pow(1 + $bungaBulanan, $jumlahBulan)) /
                (pow(1 + $bungaBulanan, $jumlahBulan) - 1);
        }

        $totalPembayaran = $dpNominal + ($cicilanBulanan * $jumlahBulan);
        $totalBunga = ($cicilanBulanan * $jumlahBulan) - $plafon;

        return [
            'metode' => MetodePembayaran::Kpr->value,
            'harga_rumah' => round($hargaRumah),
            'dp_persen' => $dpPersen,
            'dp_nominal' => round($dpNominal),
            'plafon' => round($plafon),
            'tenor_tahun' => $tenorTahun,
            'suku_bunga' => $sukuBungaPersen,
            'cicilan_bulanan' => round($cicilanBulanan),
            'total_pembayaran' => round($totalPembayaran),
            'total_bunga' => round($totalBunga),
            'amortisasi' => $this->generateAmortisasi($plafon, $sukuBungaPersen, $tenorTahun),
        ];
    }

    public function hitungCashBertahap(
        float $hargaRumah,
        float $dpPersen,
        int $tenorTahun
    ): array {
        $dpNominal = $hargaRumah * ($dpPersen / 100);
        $sisa = $hargaRumah - $dpNominal;
        $jumlahBulan = $tenorTahun * 12;

        if ($jumlahBulan <= 0) {
            throw new InvalidArgumentException('Tenor harus lebih dari 0.');
        }

        $cicilanBulanan = $sisa / $jumlahBulan;

        return [
            'metode' => MetodePembayaran::CashBertahap->value,
            'harga_rumah' => round($hargaRumah),
            'dp_persen' => $dpPersen,
            'dp_nominal' => round($dpNominal),
            'plafon' => round($sisa),
            'tenor_tahun' => $tenorTahun,
            'suku_bunga' => 0,
            'cicilan_bulanan' => round($cicilanBulanan),
            'total_pembayaran' => round($hargaRumah),
            'total_bunga' => 0,
        ];
    }

    public function hitungCashKeras(
        float $hargaRumah,
        float $diskonPersen = 0
    ): array {
        $diskonNominal = $hargaRumah * ($diskonPersen / 100);
        $totalBayar = $hargaRumah - $diskonNominal;

        return [
            'metode' => MetodePembayaran::CashKeras->value,
            'harga_rumah' => round($hargaRumah),
            'dp_persen' => 100,
            'dp_nominal' => round($totalBayar),
            'plafon' => 0,
            'tenor_tahun' => 0,
            'suku_bunga' => 0,
            'diskon_persen' => $diskonPersen,
            'diskon_nominal' => round($diskonNominal),
            'cicilan_bulanan' => 0,
            'total_pembayaran' => round($totalBayar),
            'total_bunga' => 0,
        ];
    }

    public function hitungSemuaMetode(
        float $hargaRumah,
        float $dpPersen,
        int $tenorTahun,
        float $sukuBunga,
        float $diskonCashKeras = 0
    ): array {
        return [
            'kpr' => $this->hitungKpr($hargaRumah, $dpPersen, $tenorTahun, $sukuBunga),
            'cash_bertahap' => $this->hitungCashBertahap($hargaRumah, $dpPersen, $tenorTahun),
            'cash_keras' => $this->hitungCashKeras($hargaRumah, $diskonCashKeras),
        ];
    }

    public function simpanSimulasi(array $data, int $idMarketing, ?int $idKonsumen = null): SimulasiPembayaran
    {
        return SimulasiPembayaran::create([
            'id_konsumen' => $idKonsumen,
            'id_unit' => $data['id_unit'],
            'id_marketing' => $idMarketing,
            'metode_pembayaran' => $data['metode'],
            'harga_rumah' => $data['harga_rumah'],
            'dp_persen' => $data['dp_persen'] ?? 0,
            'dp_nominal' => $data['dp_nominal'] ?? 0,
            'tenor_tahun' => $data['tenor_tahun'] ?? 0,
            'suku_bunga' => $data['suku_bunga'] ?? 0,
            'cicilan_bulanan' => $data['cicilan_bulanan'] ?? 0,
            'total_pembayaran' => $data['total_pembayaran'],
            'total_bunga' => $data['total_bunga'] ?? 0,
        ]);
    }

    public function generateAmortisasi(
        float $plafon,
        float $sukuBungaPersen,
        int $tenorTahun
    ): array {
        $bungaBulanan = $sukuBungaPersen / 100 / 12;
        $jumlahBulan = $tenorTahun * 12;

        if ($jumlahBulan <= 0) {
            return [];
        }

        if ($bungaBulanan === 0.0) {
            $cicilanBulanan = $plafon / $jumlahBulan;
        } else {
            $cicilanBulanan = $plafon *
                ($bungaBulanan * pow(1 + $bungaBulanan, $jumlahBulan)) /
                (pow(1 + $bungaBulanan, $jumlahBulan) - 1);
        }

        $sisaPokok = $plafon;
        $amortisasi = [];

        for ($bulan = 1; $bulan <= $jumlahBulan; $bulan++) {
            $bungaBulanIni = $sisaPokok * $bungaBulanan;
            $pokokBulanIni = $cicilanBulanan - $bungaBulanIni;
            $sisaPokok -= $pokokBulanIni;

            $amortisasi[] = [
                'bulan' => $bulan,
                'cicilan' => round($cicilanBulanan),
                'pokok' => round($pokokBulanIni),
                'bunga' => round($bungaBulanIni),
                'sisa_pokok' => round(max(0, $sisaPokok)),
            ];
        }

        return $amortisasi;
    }

    public function getUnitHarga(int $idUnit): float
    {
        $unit = UnitRumah::find($idUnit);

        if (! $unit) {
            throw new ModelNotFoundException('Unit tidak ditemukan.');
        }

        return (float) $unit->harga_jual;
    }
}
