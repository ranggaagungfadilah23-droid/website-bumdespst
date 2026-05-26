<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class ProductController extends Controller
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
        $produks = Produk::where('user_id', Auth::id())->latest()->get();
        return view('mitra.produk.index', compact('produks'));
    }

    public function create()
    {
        return view('mitra.produk.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga'       => 'required|numeric|min:0',
            'jumlah'      => 'required|integer|min:0',
            'deskripsi'   => 'required|string',
            'gambar'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $gambarUrl      = null;
        $gambarPublicId = null;

        if ($request->hasFile('gambar')) {
            $cloudinary     = $this->getCloudinary();
            $result         = $cloudinary->uploadApi()->upload($request->file('gambar')->getRealPath());
            $gambarUrl      = $result['secure_url'];
            $gambarPublicId = $result['public_id'];
        }

        Produk::create([
            'user_id'          => Auth::id(),
            'nama_produk'      => $request->nama_produk,
            'harga'            => $request->harga,
            'jumlah'           => $request->jumlah,
            'deskripsi'        => $request->deskripsi,
            'gambar'           => $gambarUrl,
            'gambar_public_id' => $gambarPublicId,
            'status'           => 'tersedia',
        ]);

        return redirect()->route('mitra.kelola')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function show($id)
    {
        $produk = Produk::findOrFail($id);
        return view('customer.produk.show', compact('produk'));
    }

    public function edit($id)
    {
        $produk = Produk::where('user_id', Auth::id())->findOrFail($id);
        return view('mitra.produk.edit', compact('produk'));
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga'       => 'required|numeric|min:0',
            'jumlah'      => 'required|integer|min:0',
            'deskripsi'   => 'required|string',
            'gambar'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $gambarUrl      = $produk->gambar;
        $gambarPublicId = $produk->gambar_public_id;

        if ($request->hasFile('gambar')) {
            $cloudinary = $this->getCloudinary();

            if ($produk->gambar_public_id) {
                $cloudinary->uploadApi()->destroy($produk->gambar_public_id);
            }

            $result         = $cloudinary->uploadApi()->upload($request->file('gambar')->getRealPath());
            $gambarUrl      = $result['secure_url'];
            $gambarPublicId = $result['public_id'];
        }

        $produk->update([
            'nama_produk'      => $request->nama_produk,
            'harga'            => $request->harga,
            'jumlah'           => $request->jumlah,
            'deskripsi'        => $request->deskripsi,
            'gambar'           => $gambarUrl,
            'gambar_public_id' => $gambarPublicId,
        ]);

        return redirect()->route('mitra.kelola')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $produk = Produk::where('user_id', Auth::id())->findOrFail($id);

        if ($produk->gambar_public_id) {
            $cloudinary = $this->getCloudinary();
            $cloudinary->uploadApi()->destroy($produk->gambar_public_id);
        }

        $produk->delete();

        return redirect()->back()->with('success', 'Produk berhasil dihapus!');
    }
}