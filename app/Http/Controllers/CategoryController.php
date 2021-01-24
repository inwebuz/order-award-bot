<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    private $page = '/categories';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $categories = Category::orderBy('title')->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = new Category();
        $categories = Category::whereNull('parent_id')->orderBy('title')->get();
        return view('categories.create', compact('category', 'categories'));
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
        Category::create($data);
        return redirect($this->page)->with('success', 'Category saved');
    }

    /**
     * Display the specified resource.
     *
     * @param int
     * @return \Illuminate\Http\Response
     */
    public function show($category)
    {
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $category
     * @return \Illuminate\Http\Response
     */
    public function edit($categoryID)
    {
        $category = Category::findOrFail($categoryID);
        $categories = Category::whereNull('parent_id')->where('id', '!=', $categoryID)->orderBy('title')->get();
        return view('categories.edit', compact('category','categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $categoryID)
    {
        $data = $this->validatedData($request);
        $category = Category::findOrFail($categoryID);
        $category->update($data);
        return redirect($this->page)->with('success', 'Category saved');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($categoryID)
    {
        $category = Category::findOrFail($categoryID);
        $category->delete();
        return redirect($this->page)->with('success', 'Category deleted');
    }

    private function validatedData(Request $request)
    {
        return $request->validate([
            'title' => 'required|max:191',
            'parent_id' => '',
        ]);
    }
}
