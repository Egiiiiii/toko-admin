<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // --- TAMBAHKAN BARIS INI (Jurus Anti Capek) ---
    // Artinya: Tidak ada kolom yang dijaga, semua boleh diisi.
    protected $guarded = []; 

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
