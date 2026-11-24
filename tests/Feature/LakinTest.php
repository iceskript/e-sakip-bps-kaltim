<?php

namespace Tests\Feature;

use App\Models\Lakin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LakinTest extends TestCase
{
    use RefreshDatabase;

    public function test_lakin_page_requires_authentication(): void
    {
        $this->get(route('lakin.index'))
            ->assertRedirect(route('login'));
    }

    public function test_lakin_page_shows_draft_and_final_documents_and_selects_requested_tab(): void
    {
        $this->actingAs(User::factory()->create());

        Lakin::query()->create([
            'tahun' => 2025,
            'tipe' => 'Draft',
            'judul' => 'Rancangan LAKIN 2025',
        ]);
        Lakin::query()->create([
            'tahun' => 2025,
            'tipe' => 'Final',
            'judul' => 'LAKIN Final 2025',
        ]);

        $this->get(route('lakin.index', ['tipe' => 'Final']))
            ->assertOk()
            ->assertSee('Rancangan LAKIN 2025')
            ->assertSee('LAKIN Final 2025')
            ->assertSee('Draft')
            ->assertSee('Final')
            ->assertViewHas('selectedType', 'Final');
    }

    public function test_document_link_can_be_saved_for_a_lakin_record(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('lakin.store'), [
            'tahun' => 2025,
            'tipe' => 'Draft',
            'judul' => 'Rancangan LAKIN 2025',
            'link_dokumen' => 'https://example.test/lakin-2025',
        ])
            ->assertRedirect(route('lakin.index', ['tipe' => 'Draft']))
            ->assertSessionHas('success', 'Dokumen LAKIN berhasil ditambahkan.');

        $this->assertDatabaseHas('lakins', [
            'tahun' => 2025,
            'tipe' => 'Draft',
            'judul' => 'Rancangan LAKIN 2025',
            'link_dokumen' => 'https://example.test/lakin-2025',
            'file_path' => null,
        ]);
    }

    public function test_uploaded_lakin_file_is_stored_on_the_public_disk(): void
    {
        $this->actingAs(User::factory()->create());
        Storage::fake('public');

        $this->post(route('lakin.store'), [
            'tahun' => 2026,
            'tipe' => 'Final',
            'judul' => 'LAKIN Final',
            'file' => UploadedFile::fake()->createWithContent('lakin.pdf', '%PDF-1.4 document'),
        ])
            ->assertRedirect(route('lakin.index', ['tipe' => 'Final']));

        $lakin = Lakin::query()->sole();

        $this->assertDatabaseHas('lakins', [
            'id' => $lakin->id,
            'file_path' => $lakin->file_path,
        ]);
        Storage::disk('public')->assertExists($lakin->file_path);
    }

    public function test_lakin_requires_a_file_or_a_valid_document_link(): void
    {
        $this->actingAs(User::factory()->create());

        $this->from(route('lakin.index'))
            ->post(route('lakin.store'), [
                'tahun' => 2025,
                'tipe' => 'Draft',
                'judul' => 'Rancangan tanpa dokumen',
            ])
            ->assertRedirect(route('lakin.index'))
            ->assertSessionHasErrors(['file', 'link_dokumen']);

        $this->assertDatabaseCount('lakins', 0);
    }

    public function test_lakin_rejects_non_http_document_links(): void
    {
        $this->actingAs(User::factory()->create());

        $this->from(route('lakin.index'))
            ->post(route('lakin.store'), [
                'tahun' => 2025,
                'tipe' => 'Draft',
                'judul' => 'Tautan LAKIN tidak aman',
                'link_dokumen' => 'javascript:alert(1)',
            ])
            ->assertRedirect(route('lakin.index'))
            ->assertSessionHasErrors('link_dokumen');

        $this->assertDatabaseCount('lakins', 0);
    }

    public function test_lakin_rejects_file_types_other_than_pdf_and_word_documents(): void
    {
        $this->actingAs(User::factory()->create());
        Storage::fake('public');

        $this->from(route('lakin.index'))
            ->post(route('lakin.store'), [
                'tahun' => 2025,
                'tipe' => 'Draft',
                'judul' => 'Berkas LAKIN tidak didukung',
                'file' => UploadedFile::fake()->create('payload.exe', 10, 'application/octet-stream'),
            ])
            ->assertRedirect(route('lakin.index'))
            ->assertSessionHasErrors('file');

        $this->assertDatabaseCount('lakins', 0);
        Storage::disk('public')->assertDirectoryEmpty('lakin');
    }

    public function test_lakin_document_can_be_downloaded_and_deleted(): void
    {
        $this->actingAs(User::factory()->create());
        Storage::fake('public');
        Storage::disk('public')->put('lakin/final.pdf', '%PDF-1.4 document');
        $lakin = Lakin::query()->create([
            'tahun' => 2025,
            'tipe' => 'Final',
            'judul' => 'LAKIN Final 2025',
            'file_path' => 'lakin/final.pdf',
        ]);

        $this->get(route('lakin.download', $lakin))
            ->assertOk()
            ->assertHeader('content-disposition', 'attachment; filename=lakin-final-2025.pdf');

        $this->delete(route('lakin.destroy', $lakin), ['tipe' => 'Final'])
            ->assertRedirect(route('lakin.index', ['tipe' => 'Final']))
            ->assertSessionHas('success', 'Dokumen LAKIN berhasil dihapus.');

        $this->assertDatabaseMissing('lakins', ['id' => $lakin->id]);
        Storage::disk('public')->assertMissing('lakin/final.pdf');
    }
}
