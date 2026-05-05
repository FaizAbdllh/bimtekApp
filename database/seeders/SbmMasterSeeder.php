<?php

namespace Database\Seeders;

use App\Models\SbmMaster;
use Illuminate\Database\Seeder;

class SbmMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sbmData = [
            // HONOR
            [
                'kode_sbm' => 'HON-NRS-PUSAT',
                'nama_item' => 'Honorarium Narasumber Pusat',
                'harga_satuan' => 900000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Jam (OJ)',
                'kategori' => 'honor',
                'tahun_berlaku' => 2025,
                'keterangan' => 'Sesuai PMK 32/2025 - Narasumber dari tingkat pusat',
            ],
            [
                'kode_sbm' => 'HON-MODERATOR',
                'nama_item' => 'Honorarium Moderator',
                'harga_satuan' => 700000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Kegiatan (OK)',
                'kategori' => 'honor',
                'tahun_berlaku' => 2025,
                'keterangan' => 'Sesuai PMK 32/2025',
            ],
            [
                'kode_sbm' => 'HON-PJ-PANITIA',
                'nama_item' => 'Honor Penanggung Jawab Panitia',
                'harga_satuan' => 450000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Kegiatan (OK)',
                'kategori' => 'honor',
                'tahun_berlaku' => 2025,
                'keterangan' => 'Sesuai PMK 32/2025',
            ],
            [
                'kode_sbm' => 'HON-KETUA-PANITIA',
                'nama_item' => 'Honor Ketua Panitia',
                'harga_satuan' => 400000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Kegiatan (OK)',
                'kategori' => 'honor',
                'tahun_berlaku' => 2025,
                'keterangan' => 'Sesuai PMK 32/2025',
            ],
            [
                'kode_sbm' => 'HON-SEKRETARIS',
                'nama_item' => 'Honor Sekretaris & Anggota Panitia',
                'harga_satuan' => 300000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Kegiatan (OK)',
                'kategori' => 'honor',
                'tahun_berlaku' => 2025,
                'keterangan' => 'Sesuai PMK 32/2025',
            ],

            // AKOMODASI
            [
                'kode_sbm' => 'UHAR-FULLBOARD',
                'nama_item' => 'Uang Harian Fullboard Peserta',
                'harga_satuan' => 120000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Hari (OH)',
                'kategori' => 'akomodasi',
                'tahun_berlaku' => 2025,
                'keterangan' => 'Sesuai PMK 32/2025 - Sudah termasuk makan 3x',
            ],

            // KONSUMSI
            [
                'kode_sbm' => 'KONSUMSI-RAPAT',
                'nama_item' => 'Konsumsi (Peserta, Panitia, Narasumber)',
                'harga_satuan' => 47000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Kali (OK)',
                'kategori' => 'konsumsi',
                'tahun_berlaku' => 2025,
                'keterangan' => 'Sesuai PMK 32/2025 - Per sesi/kali makan',
            ],

            // TRANSPORTASI
            [
                'kode_sbm' => 'TRANSPORT-LOKAL',
                'nama_item' => 'Uang Transpor Lokal',
                'harga_satuan' => 0,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Kali (OK)',
                'kategori' => 'transportasi',
                'tahun_berlaku' => 2025,
                'keterangan' => 'Disesuaikan dengan jarak dan wilayah',
            ],

            // ATK
            [
                'kode_sbm' => 'ATK-PESERTA',
                'nama_item' => 'ATK Peserta',
                'harga_satuan' => 0,
                'satuan_primary' => 'Paket',
                'satuan_secondary' => 'Orang',
                'kategori' => 'atk',
                'tahun_berlaku' => 2025,
                'keterangan' => 'Disesuaikan dengan kebutuhan',
            ],

            // SEWA
            [
                'kode_sbm' => 'SEWA-RUANGAN',
                'nama_item' => 'Sewa Ruang Pertemuan',
                'harga_satuan' => 0,
                'satuan_primary' => 'Paket',
                'satuan_secondary' => 'Hari (PH)',
                'kategori' => 'sewa',
                'tahun_berlaku' => 2025,
                'keterangan' => 'Disesuaikan dengan kapasitas dan fasilitas',
            ],
        ];

        foreach ($sbmData as $data) {
            SbmMaster::create($data);
        }

        $this->command->info('SBM Master data seeded successfully!');
    }
}
