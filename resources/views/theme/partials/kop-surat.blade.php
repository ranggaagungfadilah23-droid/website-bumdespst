@php
    $logoBase64 = '';
    if (extension_loaded('gd')) {
        $logoPath = public_path('asset/img/logoBumdes.png');
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }
    }
@endphp

<div class="kop-surat">
    <div class="kop-logo">
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" alt="Logo BUMDes">
        @endif
    </div>
    <div class="kop-teks">
        <p class="instansi">Badan Usaha Milik Desa</p>
        <p class="nama-bumdes">BUMDes Putra Samudra Patimban</p>
        <p class="alamat">
            Sekretariat: Jl. PUK No.114, Desa Patimban, Kec. Pusakanagara, Kab. Subang, Jawa Barat<br>
            Email: bumdespatimban9@gmail.com
        </p>
    </div>
</div>
<hr class="garis-kop">
<hr class="garis-kop-tipis">