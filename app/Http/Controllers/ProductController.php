<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    private $page = '/products';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $products = Product::with('category')->latest()->paginate(50);
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $product = new Product();
        $categories = $this->categories();
        return view('products.create', compact('product', 'categories'));
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
        // $data = $this->validatedData($request, [
        //     'image' => 'required|image',
        // ]);
        // $data['image_file_id'] = '';

        // $urlImage = $request->file('image');
        // if ($urlImage) {
        //     $data['image'] = $urlImage->store('', 'public');
        // }

        Product::create($data);
        return redirect($this->page)->with('success', 'Product saved');
    }

    /**
     * Display the specified resource.
     *
     * @param int $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $product
     * @return \Illuminate\Http\Response
     */
    public function edit($productID)
    {
        $product = Product::with('category')->findOrFail($productID);
        $categories = $this->categories();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $productID)
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

    /**
     * Remove the specified resource from storage.
     *
     * @param int $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($productID)
    {
        // delete old image
        $product = Product::findOrFail($productID);
        // if ($product->image) {
        //     Storage::disk('public')->delete($product->image);
        // }
        $product->delete();
        return redirect($this->page)->with('success', 'Product deleted');
    }

    private function validatedData(Request $request, $options = [])
    {
        $rules = [
            'name' => 'required|max:191',
            'button_text' => 'required|max:191',
            'units' => 'required|max:191',
            'description' => 'max:191',
            'price' => 'required|numeric',
            'category_id' => 'integer',
            //'image' => 'image',
        ];
        $rules = array_merge($rules, $options);
        return $request->validate($rules, [
            '*.required' => __('Required field'),
            '*.image' => __('Upload an image'),
        ]);
    }

    private function categories()
    {
        return Category::orderBy('name')->get();
    }
}
