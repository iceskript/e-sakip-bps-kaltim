<?php

namespace App\Http\Controllers;

use App\Models\Lakin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LakinController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'tipe' => ['nullable', Rule::in(Lakin::TYPES)],
        ]);
        $lakins = Lakin::query()
            ->orderByDesc('tahun')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view('lakin.index', [
            'lakinsByType' => $lakins->groupBy('tipe'),
            'selectedType' => $validated['tipe'] ?? 'Draft',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'tipe' => ['required', Rule::in(Lakin::TYPES)],
            'judul' => ['required', 'string', 'max:255'],
            'link_dokumen' => ['nullable', 'url:http,https', 'max:2048', 'required_without:file'],
            'file' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx',
                'extensions:pdf,doc,docx',
                'max:20480',
                'required_without:link_dokumen',
            ],
            'keterangan' => ['nullable', 'string', 'max:5000'],
        ]);

        $uploadedFile = $request->file('file');
        $filePath = $uploadedFile?->storePublicly('lakin', 'public');

        if ($uploadedFile !== null && ! is_string($filePath)) {
            throw new RuntimeException('File LAKIN gagal disimpan.');
        }

        Lakin::query()->create([
            'tahun' => (int) $validated['tahun'],
            'tipe' => $validated['tipe'],
            'judul' => $validated['judul'],
            'link_dokumen' => $validated['link_dokumen'] ?? null,
            'file_path' => $filePath,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return to_route('lakin.index', ['tipe' => $validated['tipe']])
            ->with('success', 'Dokumen LAKIN berhasil ditambahkan.');
    }

    public function download(Lakin $lakin): StreamedResponse
    {
        abort_unless(
            filled($lakin->file_path) && Storage::disk('public')->exists($lakin->file_path),
            404,
        );

        $extension = pathinfo($lakin->file_path, PATHINFO_EXTENSION);
        $filename = Str::slug($lakin->judul).($extension === '' ? '' : '.'.$extension);

        return Storage::disk('public')->download($lakin->file_path, $filename);
    }

    public function destroy(Request $request, Lakin $lakin): RedirectResponse
    {
        $validated = $request->validate([
            'tipe' => ['nullable', Rule::in(Lakin::TYPES)],
        ]);

        if ($lakin->file_path !== null && Storage::disk('public')->exists($lakin->file_path)) {
            if (! Storage::disk('public')->delete($lakin->file_path)) {
                throw new RuntimeException('File LAKIN gagal dihapus.');
            }
        }

        $lakin->delete();

        return to_route('lakin.index', ['tipe' => $validated['tipe'] ?? $lakin->tipe])
            ->with('success', 'Dokumen LAKIN berhasil dihapus.');
    }
}
