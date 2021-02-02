<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('profile.index', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $data = $this->validatedData($request);
        $product = Product::findOrFail($productID);
        // $urlImage = $request->file('image');
        // if ($urlImage) {
        //     // delete old image
        //     if ($product->image) {
        //         Storage::disk('public')->delete($product->image);
        //     }
        //     // save new image
        //     $data['image'] = $urlImage->store('', 'public');
        // }
        $product->update($data);
        return redirect($this->page)->with('success', 'Product saved');
    }
}
