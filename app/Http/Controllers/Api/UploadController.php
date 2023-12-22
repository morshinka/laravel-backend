<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        // menambahkan statment untuk mengatasi eror
        // data harus berupa gambar

        if ($request->has('image')) {
            // untuk mengangkap request dari image yang dikirim
            $image = $request->image;

            // time digunakan untuk memberikan nama unik
            // getClientOriginalExtension() digunakan untuk menangkap format gambar seperti jpg,png,dll
            $nameFile = time() . '.' . $image->getClientOriginalExtension();

            // didalam laravel folder yang digunakan untuk menyimpan gambar adalah path = public_path(create folder)
            $path = public_path('upload/image');

            // setelah ini saya perlu memindahkan agar image di pindahkan ke folder upload_image
            $image->move($path, $nameFile);

            // pengembalian nilai dari image
            return response()->json([
                'image_path' => '/upload/image/' . $nameFile,
                'base_url' => url('/'),
            ]);
        }
    }

    // function untuk menangani upload image lebih dari satu
    public function uploadMultipleImage(Request $request)
    {
        if ($request->has('image')) {
            $images = $request->image;
            foreach ($images as $key => $image) {
                $nameFile = time() . $key  . '.' . $image->getClientOriginalExtension();
                $path = public_path('upload/image');
                $image->move($path, $nameFile);
            }

            return response()->json([
                'status' => 'Succsesfully',
            ]);
        }
    }
}
