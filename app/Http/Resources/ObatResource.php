<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ObatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
        'id'             => $this->id_obat, // Memetakan id_obat ke 'id' agar seragam
        'kode_obat'      => $this->kode, // Mengambil dari getKodeAttribute di modelmu
        'nama'           => $this->nama_obat,
        'jenis'          => $this->jenis_obat,
        'stok_aktual'    => $this->stok,
        'status_stok'    => $this->getStatusStok()['label'], // Mengambil label dari fungsi modelmu
        'harga'          => $this->harga_beli,
        'kadaluarsa'     => $this->tgl_kadaluarsa->format('Y-m-d'),
        'sisa_hari_exp'  => $this->hitungSisaHariKadaluarsa(),
        ];
    }
}
