<?php

namespace Tests\Unit;

use App\Models\Bimtek;
use App\Models\DokumenPersyaratanPeserta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DokumenPersyaratanPesertaTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_bimtek()
    {
        $bimtek = Bimtek::factory()->create();
        $user = User::factory()->create();

        $dokumen = DokumenPersyaratanPeserta::create([
            'bimtek_id' => $bimtek->id,
            'user_id' => $user->id,
            'jenis_dokumen' => 'surat_tugas',
            'file_path' => 'test/file.pdf',
            'file_name' => 'file.pdf',
            'status' => 'pending',
            'uploaded_at' => now(),
        ]);

        $this->assertInstanceOf(Bimtek::class, $dokumen->bimtek);
        $this->assertEquals($bimtek->id, $dokumen->bimtek->id);
    }

    #[Test]
    public function it_belongs_to_user_uploader()
    {
        $bimtek = Bimtek::factory()->create();
        $user = User::factory()->create();

        $dokumen = DokumenPersyaratanPeserta::create([
            'bimtek_id' => $bimtek->id,
            'user_id' => $user->id,
            'jenis_dokumen' => 'surat_tugas',
            'file_path' => 'test/file.pdf',
            'file_name' => 'file.pdf',
            'status' => 'pending',
            'uploaded_at' => now(),
        ]);

        $this->assertInstanceOf(User::class, $dokumen->user);
        $this->assertEquals($user->id, $dokumen->user->id);
    }

    #[Test]
    public function it_belongs_to_verifier()
    {
        $bimtek = Bimtek::factory()->create();
        $user = User::factory()->create();
        $verifier = User::factory()->create();

        $dokumen = DokumenPersyaratanPeserta::create([
            'bimtek_id' => $bimtek->id,
            'user_id' => $user->id,
            'jenis_dokumen' => 'surat_tugas',
            'file_path' => 'test/file.pdf',
            'file_name' => 'file.pdf',
            'status' => 'approved',
            'verified_by' => $verifier->id,
            'uploaded_at' => now(),
        ]);

        $this->assertInstanceOf(User::class, $dokumen->verifier);
        $this->assertEquals($verifier->id, $dokumen->verifier->id);
    }

    #[Test]
    public function is_approved_method_returns_true_when_status_approved()
    {
        $dokumen = new DokumenPersyaratanPeserta(['status' => 'approved']);
        $this->assertTrue($dokumen->isApproved());

        $dokumen = new DokumenPersyaratanPeserta(['status' => 'pending']);
        $this->assertFalse($dokumen->isApproved());
    }

    #[Test]
    public function is_rejected_method_returns_true_when_status_rejected()
    {
        $dokumen = new DokumenPersyaratanPeserta(['status' => 'rejected']);
        $this->assertTrue($dokumen->isRejected());

        $dokumen = new DokumenPersyaratanPeserta(['status' => 'approved']);
        $this->assertFalse($dokumen->isRejected());
    }

    #[Test]
    public function is_pending_method_returns_true_when_status_pending()
    {
        $dokumen = new DokumenPersyaratanPeserta(['status' => 'pending']);
        $this->assertTrue($dokumen->isPending());

        $dokumen = new DokumenPersyaratanPeserta(['status' => 'approved']);
        $this->assertFalse($dokumen->isPending());
    }

    #[Test]
    public function get_file_url_returns_correct_url()
    {
        $dokumen = new DokumenPersyaratanPeserta([
            'file_path' => 'dokumen_persyaratan/test.pdf',
        ]);

        $expectedUrl = Storage::url('dokumen_persyaratan/test.pdf');
        $this->assertEquals($expectedUrl, $dokumen->file_url);
    }

    #[Test]
    public function casts_uploaded_at_as_datetime()
    {
        $bimtek = Bimtek::factory()->create();
        $user = User::factory()->create();

        $dokumen = DokumenPersyaratanPeserta::create([
            'bimtek_id' => $bimtek->id,
            'user_id' => $user->id,
            'jenis_dokumen' => 'surat_tugas',
            'file_path' => 'test/file.pdf',
            'file_name' => 'file.pdf',
            'status' => 'pending',
            'uploaded_at' => '2026-02-04 10:00:00',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $dokumen->uploaded_at);
    }

    #[Test]
    public function casts_verified_at_as_datetime()
    {
        $bimtek = Bimtek::factory()->create();
        $user = User::factory()->create();

        $dokumen = DokumenPersyaratanPeserta::create([
            'bimtek_id' => $bimtek->id,
            'user_id' => $user->id,
            'jenis_dokumen' => 'surat_tugas',
            'file_path' => 'test/file.pdf',
            'file_name' => 'file.pdf',
            'status' => 'approved',
            'verified_at' => '2026-02-04 11:00:00',
            'uploaded_at' => now(),
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $dokumen->verified_at);
    }
}
