<?php

namespace App\Http\Controllers;

use App\Http\Resources\KartuStokResource;
use App\Models\KartuStok;
use Illuminate\Http\Request;

class KartuStokController extends Controller
{
    public function kartuStok($nomor_sku){
        $query = KartuStok::where('nomor_sku', $nomor_sku)->orderBy('created_at', 'DESC')->paginate(10);
        return KartuStokResource::collection($query);
    }
}
