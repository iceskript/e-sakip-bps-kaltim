<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'in:FRA,KKPK,Renstra,LAKIN'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
        ], [
            'judul.required' => 'Nama kegiatan wajib diisi.',
            'judul.max' => 'Nama kegiatan tidak boleh melebihi 255 karakter.',
            'kategori.required' => 'Kategori wajib dipilih.',
            'kategori.in' => 'Kategori yang dipilih tidak valid.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date' => 'Tanggal mulai harus berupa tanggal yang valid.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.date' => 'Tanggal selesai harus berupa tanggal yang valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama dengan atau setelah tanggal mulai.',
            'deskripsi.string' => 'Keterangan harus berupa teks.',
            'deskripsi.max' => 'Keterangan tidak boleh melebihi 2.000 karakter.',
        ]);

        Jadwal::create($validated);

        return redirect()
            ->route('dashboard', ['month' => substr($validated['tanggal_mulai'], 0, 7)])
            ->with('status', 'Kegiatan berhasil ditambahkan.');
    }

    public function destroy(Jadwal $jadwal): RedirectResponse
    {
        $jadwal->delete();

        return redirect()
            ->back()
            ->with('status', 'Kegiatan berhasil dihapus.');
    }

    public function updateCompletion(Request $request, Jadwal $jadwal): RedirectResponse
    {
        $validated = $request->validate([
            'selesai' => ['required', 'boolean'],
        ]);

        $jadwal->update(['selesai' => $validated['selesai']]);

        return redirect()
            ->back()
            ->with('status', $jadwal->selesai
                ? 'Kegiatan ditandai selesai.'
                : 'Kegiatan dikembalikan ke daftar belum selesai.');
    }
}
