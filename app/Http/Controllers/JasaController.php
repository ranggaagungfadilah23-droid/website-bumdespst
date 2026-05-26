<?php

namespace App\Http\Controllers;

use App\Models\Jasa;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class JasaController extends Controller
{
    private function getCloudinary()
    {
        return new Cloudinary(
            Configuration::instance([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key'    => env('CLOUDINARY_API_KEY'),
                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                ],
                'url' => ['secure' => true]
            ])
        );
    }

    public function index()
    {
        $jasas = Jasa::where('user_id', Auth::id())->latest()->get();
        return view('mitra.jasa.index', compact('jasas'));
    }

    public function landingPage()
    {
        $jasas   = Jasa::latest()->take(3)->get();
        $produks = Produk::latest()->take(3)->get();
        return view('index', compact('jasas', 'produks'));
    }

    public function dashboard()
    {
        $jasas   = Jasa::all();
        $produks = Produk::all();
        return view('customer.dashboard', compact('jasas', 'produks'));
    }

    public function create()
    {
        return view('mitra.jasa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jasa' => 'required|string|max:255',
            'harga'     => 'required|numeric|min:0',
            'satuan'    => 'required|in:Layanan,Jam,Hari',
            'deskripsi' => 'required|string',
            'gambar'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $gambarUrl      = null;
        $gambarPublicId = null;

        if ($request->hasFile('gambar')) {
            $cloudinary     = $this->getCloudinary();
            $result         = $cloudinary->uploadApi()->upload($request->file('gambar')->getRealPath());
            $gambarUrl      = $result['secure_url'];
            $gambarPublicId = $result['public_id'];
        }

        Jasa::create([
            'user_id'          => Auth::id(),
            'nama_jasa'        => $request->nama_jasa,
            'harga'            => $request->harga,
            'satuan'           => $request->satuan,
            'deskripsi'        => $request->deskripsi,
            'gambar'           => $gambarUrl,
            'gambar_public_id' => $gambarPublicId,
            'status'           => 'aktif',
        ]);

        return redirect()->route('mitra.kelola')->with('success', 'Layanan jasa berhasil ditambahkan!');
    }

    public function show($id)
    {
        $jasa = Jasa::findOrFail($id);
        return view('customer.jasa.show', compact('jasa'));
    }

    public function edit($id)
    {
        $jasa = Jasa::where('user_id', Auth::id())->findOrFail($id);
        return view('mitra.jasa.edit', compact('jasa'));
    }

    public function update(Request $request, $id)
    {
        $jasa = Jasa::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'nama_jasa' => 'required|string|max:255',
            'harga'     => 'required|numeric|min:0',
            'satuan'    => 'required|in:Layanan,Jam,Hari',
            'deskripsi' => 'required|string',
            'gambar'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $gambarUrl      = $jasa->gambar;
        $gambarPublicId = $jasa->gambar_public_id;

        if ($request->hasFile('gambar')) {
            $cloudinary = $this->getCloudinary();

            if ($jasa->gambar_public_id) {
                $cloudinary->uploadApi()->destroy($jasa->gambar_public_id);
            }

            $result         = $cloudinary->uploadApi()->upload($request->file('gambar')->getRealPath());
            $gambarUrl      = $result['secure_url'];
            $gambarPublicId = $result['public_id'];
        }

        $jasa->update([
            'nama_jasa'        => $request->nama_jasa,
            'harga'            => $request->harga,
            'satuan'           => $request->satuan,
            'deskripsi'        => $request->deskripsi,
            'gambar'           => $gambarUrl,
            'gambar_public_id' => $gambarPublicId,
        ]);

        return redirect()->route('mitra.kelola')->with('success', 'Layanan jasa berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $jasa = Jasa::where('user_id', Auth::id())->findOrFail($id);

        if ($jasa->gambar_public_id) {
            $cloudinary = $this->getCloudinary();
            $cloudinary->uploadApi()->destroy($jasa->gambar_public_id);
        }

        $jasa->delete();

        return redirect()->back()->with('success', 'Layanan jasa berhasil dihapus!');
    }
}