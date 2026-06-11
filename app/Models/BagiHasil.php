<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class BagiHasil extends Model
{
    use LogsActivity;

    protected $table = 'bagihasils';

    protected $fillable = [
        'mitra_id',
        'total_omzet',
        'persen_bumdes',
        'persen_mitra',
        'nominal_bumdes',
        'nominal_mitra',
        'status',
        'tanggal'
    ];

    /**
     * Relasi ke model Mitra.
     * Menggunakan mitra_id (dari tabel bagihasils) yang merujuk ke user_id (di tabel mitras).
     * Ini adalah kunci agar pemanggilan data tidak melakukan query berulang (N+1).
     */
    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'mitra_id', 'user_id');
    }

    /**
     * Konfigurasi Activity Log.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'total_omzet', 'nominal_bumdes'])
            ->setDescriptionForEvent(fn(string $eventName) => "Bagi hasil {$eventName}")
            ->useLogName('bagihasil');
    }
}