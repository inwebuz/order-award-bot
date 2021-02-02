<?php

namespace App\Http\Controllers;

use App\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GalleryController extends Controller
{
    private $page = '/galleries';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $galleries = Gallery::latest()->paginate(50);
        return view('galleries.index', compact('galleries'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $gallery = new Gallery();
        return view('galleries.create', compact('gallery'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['image_file_id'] = '';

        $urlImage = $request->file('image');
        if ($urlImage) {
            $data['image'] = $urlImage->store('galleries', 'public');
        }

        Gallery::create($data);
        return redirect($this->page)->with('success', 'Gallery saved');
    }

    /**
     * Display the specified resource.
     *
     * @param int $gallery
     * @return \Illuminate\Http\Response
     */
    public function show($gallery)
    {
        return view('galleries.show', compact('gallery'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $gallery
     * @return \Illuminate\Http\Response
     */
    public function edit($galleryID)
    {
        $gallery = Gallery::findOrFail($galleryID);
        return view('galleries.edit', compact('gallery'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $gallery
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $galleryID)
    {
        $data = $this->validatedData($request);
        $gallery = Gallery::findOrFail($galleryID);
        $urlImage = $request->file('image');
        if ($urlImage) {
            // delete old image
            if ($gallery->image) {
                Storage::disk('public')->delete($gallery->image);
            }
            // save new image
            $data['image'] = $urlImage->store('galleries', 'public');
        }
        $gallery->update($data);
        return redirect($this->page)->with('success', 'Gallery saved');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $gallery
     * @return \Illuminate\Http\Response
     */
    public function destroy($galleryID)
    {
        // delete old image
        $gallery = Gallery::findOrFail($galleryID);
        if ($gallery->image) {
            Storage::disk('public')->delete($gallery->image);
        }
        $gallery->delete();
        return redirect($this->page)->with('success', 'Gallery deleted');
    }

    private function validatedData(Request $request, $options = [])
    {
        $rules = [
            'name' => 'required|max:191',
            'image' => 'image',
        ];
        $rules = array_merge($rules, $options);
        return $request->validate($rules, [
            '*.required' => __('Required field'),
            '*.image' => __('Upload an image'),
        ]);
    }
}
