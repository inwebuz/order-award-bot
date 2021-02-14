<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        // $request->validate([
        //     'current_password' => ['required', ],
        //     'new_password' => 'required|min:6|confirmed',
        // ]);

        // dd($request->all());

        // return redirect()->route('profile.index')->withSuccess(__('Profile updated'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|password',
            'new_password' => 'required|min:6|confirmed',
        ]);

        auth()->user()->update(['password' => Hash::make($request->new_password)]);

        return redirect()->route('profile.index')->withSuccess(__('Profile updated'));
    }
}
