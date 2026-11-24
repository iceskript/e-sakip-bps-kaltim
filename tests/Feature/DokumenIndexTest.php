<?php

namespace Tests\Feature;

use App\Models\Dokumen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DokumenIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_pages_require_authentication(): void
    {
        $this->get(route('dokumens.index', ['menuKey' => 'renstra']))
            ->assertRedirect(route('login'));
    }

    public function test_renstra_page_filters_documents_by_year_and_shows_safe_links(): void
    {
        $this->actingAs(User::factory()->create());

        Dokumen::factory()->create([
            'menu_key' => 'renstra',
            'judul' => 'Renstra 2025-2029',
            'tahun' => 2025,
            'url_link' => 'https://example.com/renstra',
        ]);
        Dokumen::factory()->create([
            'menu_key' => 'renstra',
            'judul' => 'Reviu Target Renstra 2026',
            'tahun' => 2026,
        ]);
        Dokumen::factory()->create([
            'menu_key' => 'renstra',
            'judul' => 'Alamat tidak aman',
            'tahun' => 2025,
            'url_link' => 'javascript:alert(1)',
        ]);
        Dokumen::factory()->create([
            'menu_key' => 'perjanjian_kinerja',
            'judul' => 'Perjanjian Kinerja Tahun 2025',
            'tahun' => 2025,
        ]);

        $this->get(route('dokumens.index', ['menuKey' => 'renstra', 'tahun' => 2025]))
            ->assertOk()
            ->assertSee('Renstra 2025-2029')
            ->assertSee('https://example.com/renstra', false)
            ->assertSee('Alamat tidak aman')
            ->assertDontSee('Reviu Target Renstra 2026')
            ->assertDontSee('Perjanjian Kinerja Tahun 2025')
            ->assertDontSee('javascript:alert(1)', false)
            ->assertViewHas('selectedYear', 2025);
    }

    public function test_perjanjian_kinerja_page_shows_its_own_documents_and_empty_state(): void
    {
        $this->actingAs(User::factory()->create());

        Dokumen::factory()->create([
            'menu_key' => 'perjanjian_kinerja',
            'judul' => 'Perjanjian Kinerja Tahun 2026',
            'tahun' => 2026,
            'url_link' => null,
        ]);
        Dokumen::factory()->create([
            'menu_key' => 'renstra',
            'judul' => 'Renstra document for another menu',
            'tahun' => 2026,
        ]);

        $this->get(route('dokumens.index', ['menuKey' => 'perjanjian_kinerja']))
            ->assertOk()
            ->assertSee('Perjanjian Kinerja Tahun 2026')
            ->assertSee('Belum tersedia')
            ->assertDontSee('Renstra document for another menu');
    }

    public function test_file_documents_use_the_public_storage_url(): void
    {
        $this->actingAs(User::factory()->create());
        Storage::fake('public');
        Storage::disk('public')->put('renstra/renstra.pdf', 'document');

        Dokumen::factory()->create([
            'menu_key' => 'renstra',
            'judul' => 'Renstra PDF',
            'jenis' => 'File',
            'url_link' => null,
            'file_path' => 'renstra/renstra.pdf',
            'filesize' => '8 KB',
        ]);

        $this->get(route('dokumens.index', ['menuKey' => 'renstra']))
            ->assertOk()
            ->assertSee(Storage::disk('public')->url('renstra/renstra.pdf'), false)
            ->assertSee('Download');
    }
}
