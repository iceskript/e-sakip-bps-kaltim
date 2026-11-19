<?php

namespace App\Http\Controllers;

use App\Models\IkssMaster;

class IkssController extends Controller
{
    public function index()
    {
        // Mengambil semua data IKSS, diurutkan berdasarkan kode
        $ikss = IkssMaster::orderBy('kode_indikator', 'asc')->get();

        // Me-render view 'ikss.index' dan mengirim data $ikss
        return view('ikss.index', compact('ikss'));
    }
}
