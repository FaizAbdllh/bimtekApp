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
            // HONORARIUM
            [
                'kode_sbm' => 'HON-PENANGGUNGJAWAB-PANITIA',
                'nama_item' => 'Honor Penanggungjawab Panitia',
                'harga_satuan' => 450000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Kegiatan',
                'kategori' => 'honor',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Maksimal 1 orang per kegiatan.',
            ],
            [
                'kode_sbm' => 'HON-KETUA-PANITIA',
                'nama_item' => 'Honor Ketua Panitia',
                'harga_satuan' => 400000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Kegiatan',
                'kategori' => 'honor',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Maksimal 1 orang per kegiatan.',
            ],
            [
                'kode_sbm' => 'HON-SEKRETARIS-ANGGOTA',
                'nama_item' => 'Honor Sekretaris & Anggota Panitia',
                'harga_satuan' => 300000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Kegiatan',
                'kategori' => 'honor',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Total panitia maksimal 10% dari jumlah peserta.',
            ],
            [
                'kode_sbm' => 'HON-MODERATOR',
                'nama_item' => 'Honorarium Moderator',
                'harga_satuan' => 700000,
                'satuan_primary' => 'Kali Tampil',
                'satuan_secondary' => 'Kegiatan',
                'kategori' => 'honor',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Berdasarkan jam pelaksanaan kelas.',
            ],
            [
                'kode_sbm' => 'HON-NARASUMBER-ESELON-II',
                'nama_item' => 'Honor Narasumber (Eselon II)',
                'harga_satuan' => 1400000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Jam (OJ)',
                'kategori' => 'honor',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Berlaku untuk pejabat setingkat Direktur/Kepala Dinas.',
            ],
            [
                'kode_sbm' => 'HON-NARASUMBER-PAKAR',
                'nama_item' => 'Honor Narasumber Pakar/Menteri/Eselon I',
                'harga_satuan' => 1700000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Jam (OJ)',
                'kategori' => 'honor',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Honorarium untuk narasumber tingkat Pusat/Eselon I.',
            ],
            [
                'kode_sbm' => 'HON-NARASUMBER-FUNGSIONAL',
                'nama_item' => 'Honor Narasumber (Fungsional/Praktisi)',
                'harga_satuan' => 900000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Jam (OJ)',
                'kategori' => 'honor',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Berlaku untuk Widyaprada, Guru Penggerak, atau Ahli.',
            ],

            // UANG HARIAN
            [
                'kode_sbm' => 'UHAR-LUAR-KOTA-SUMBAR',
                'nama_item' => 'Perjalanan Dinas Biasa (Luar Kota Sumbar)',
                'harga_satuan' => 370000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Hari (OH)',
                'kategori' => 'akomodasi',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Diberikan jika peserta menginap di Mess BBPMP.',
            ],
            [
                'kode_sbm' => 'UHAR-DALAM-KOTA-8JAM',
                'nama_item' => 'Perjalanan Dinas Dalam Kota (>8 Jam)',
                'harga_satuan' => 150000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Hari (OH)',
                'kategori' => 'akomodasi',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Untuk kegiatan non-meeting paket di kantor.',
            ],
            [
                'kode_sbm' => 'UHAR-FULLBOARD-LUAR',
                'nama_item' => 'Paket Fullboard Hotel (Luar Kota)',
                'harga_satuan' => 120000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Hari (OH)',
                'kategori' => 'akomodasi',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Uang saku peserta Kab./Kota selama menginap di hotel.',
            ],
            [
                'kode_sbm' => 'UHAR-FULLBOARD-DALAM-PADANG',
                'nama_item' => 'Paket Fullboard Hotel (Dalam Kota Padang)',
                'harga_satuan' => 85000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Hari (OH)',
                'kategori' => 'akomodasi',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Uang saku peserta lokal Padang jika ikut menginap.',
            ],

            // AKOMODASI & MEETING PACKAGE
            [
                'kode_sbm' => 'AKOM-FULLBOARD-BINTANG3',
                'nama_item' => 'Paket Fullboard Hotel (Minimal Bintang 3)',
                'harga_satuan' => 750000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Hari (OH)',
                'kategori' => 'akomodasi',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Sudah mencakup kamar, makan, dan ruang rapat.',
            ],
            [
                'kode_sbm' => 'MEET-FULLDAY-NOMALZ',
                'nama_item' => 'Paket Fullday Hotel (Tanpa Menginap)',
                'harga_satuan' => 300000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Hari (OH)',
                'kategori' => 'akomodasi',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Untuk paket pertemuan minimal 8 jam di hotel.',
            ],
            [
                'kode_sbm' => 'AKOM-PENGINAPAN-ECERAN',
                'nama_item' => 'Satuan Biaya Penginapan Hotel (Eceran)',
                'harga_satuan' => 650000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Malam',
                'kategori' => 'akomodasi',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Jika kamar dipisah dari paket meeting resmi.',
            ],
            [
                'kode_sbm' => 'SEWA-AULA-EXTERNAL',
                'nama_item' => 'Sewa Ruang Aula / Kelas Eksternal',
                'harga_satuan' => 1000000,
                'satuan_primary' => 'Hari',
                'satuan_secondary' => null,
                'kategori' => 'sewa',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Hanya aktif jika menggunakan aset pihak ketiga.',
            ],

            // KONSUMSI (SWAKELOLA)
            [
                'kode_sbm' => 'KONSUMSI-MAKAN-PORSI',
                'nama_item' => 'Konsumsi Kegiatan Swakelola (Makan)',
                'harga_satuan' => 47000,
                'satuan_primary' => 'Porsi',
                'satuan_secondary' => null,
                'kategori' => 'konsumsi',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Aktif hanya jika acara di Aula/Gedung BBPMP.',
            ],
            [
                'kode_sbm' => 'KONSUMSI-SNACK-PORSI',
                'nama_item' => 'Konsumsi Kegiatan Swakelola (Snack)',
                'harga_satuan' => 19000,
                'satuan_primary' => 'Porsi',
                'satuan_secondary' => null,
                'kategori' => 'konsumsi',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Aktif hanya jika acara di Aula/Gedung BBPMP.',
            ],

            // LOGISTIK & TRANSPORT
            [
                'kode_sbm' => 'TRANSPOR-LOKAL-PADANG',
                'nama_item' => 'Uang Transpor Lokal (Dalam Kota Padang)',
                'harga_satuan' => 150000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Kali',
                'kategori' => 'transportasi',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Tarif flat transpor lokal peserta/panitia.',
            ],
            [
                'kode_sbm' => 'TIKET-JKT-PDG',
                'nama_item' => 'Tiket Pesawat Jakarta - Padang (Ekonomi)',
                'harga_satuan' => 1611000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Tiket',
                'kategori' => 'transportasi',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Bersifat At Cost (dibayar sesuai kuitansi riil).',
            ],
            [
                'kode_sbm' => 'TAKSI-BANDARA-MINANGKABAU',
                'nama_item' => 'Taksi Bandara Internasional Minangkabau',
                'harga_satuan' => 120000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Kali',
                'kategori' => 'transportasi',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Tarif perjalanan dari/ke bandara BIM.',
            ],
            [
                'kode_sbm' => 'SEWA-KENDARAAN-BUS',
                'nama_item' => 'Sewa Kendaraan Operasional / Bus (Lump Sum per Hari)',
                'harga_satuan' => 850000,
                'satuan_primary' => 'Hari',
                'satuan_secondary' => null,
                'kategori' => 'sewa',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Sewa kendaraan operasional atau bus jika diperlukan.',
            ],
            [
                'kode_sbm' => 'ATK-SEMINAR-KIT',
                'nama_item' => 'ATK / Perlengkapan Peserta (Seminar Kit)',
                'harga_satuan' => 50000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => 'Paket',
                'kategori' => 'atk',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Batas atas tas, buku, dan alat tulis peserta.',
            ],
            [
                'kode_sbm' => 'CETAK-SPANDUK-BACKDROP',
                'nama_item' => 'Biaya Cetak Spanduk / Backdrop',
                'harga_satuan' => 150000,
                'satuan_primary' => 'Meter',
                'satuan_secondary' => 'Paket',
                'kategori' => 'lainnya',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Validasi berdasarkan harga pasar wajar di Padang.',
            ],
            [
                'kode_sbm' => 'UANG-SAKU-PAKET-DATA',
                'nama_item' => 'Uang Saku Pengganti Paket Data Peserta',
                'harga_satuan' => 150000,
                'satuan_primary' => 'Orang',
                'satuan_secondary' => null,
                'kategori' => 'lainnya',
                'tahun_berlaku' => 2026,
                'keterangan' => 'Uang saku pengganti paket data untuk peserta jika kegiatan hybrid/daring.',
            ],
        ];

        foreach ($sbmData as $data) {
            SbmMaster::updateOrCreate(
                ['kode_sbm' => $data['kode_sbm']],
                $data
            );
        }

        $this->command->info('SBM Master data seeded successfully!');
    }
}
