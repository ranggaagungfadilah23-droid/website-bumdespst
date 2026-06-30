<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Mitra extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'nama_usaha',
        'nama_pemilik',
        'no_hp',
        'nik',
        'jenis_usaha',
        'alamat_usaha',
        'sku',
        'dusun',
        'status',

    ];
        use LogsActivity;
      public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'nama_usaha'])
            ->setDescriptionForEvent(fn(string $eventName) => "Data mitra {$eventName}")
            ->useLogName('mitra');
    }

    /**
     * Relasi balik ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Jasa (Satu mitra memiliki banyak jasa)
     */
    public function jasas()
    {
        return $this->hasMany(Jasa::class, 'user_id');
    }

    public function products()
    {
        return $this->hasMany(Produk::class, 'user_id');
    }
}
