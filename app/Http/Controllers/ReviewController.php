<?php

namespace App\Http\Controllers;

use App\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use stdClass;

class ReviewController extends Controller
{
    private $table = 'reviews';
    private $page = '/reviews';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $reviews = Review::latest()->paginate(50);

        return view('reviews.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $review = new Review();
        $statuses = Review::statuses();
        return view('reviews.create', compact('review', 'statuses'));
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
        Review::create($data);
        return redirect($this->page)->with('success', 'Review saved');
    }

    /**
     * Display the specified resource.
     *
     * @param int
     * @return \Illuminate\Http\Response
     */
    public function show($review)
    {
        return view('reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $review
     * @return \Illuminate\Http\Response
     */
    public function edit($reviewID)
    {
        $review = Review::findOrFail($reviewID);
        $statuses = Review::statuses();
        return view('reviews.edit', compact('review', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $review
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $reviewID)
    {
        $data = $this->validatedData($request);
        $review = Review::findOrFail($reviewID);
        $review->update($data);
        return redirect($this->page)->with('success', 'Review saved');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $review
     * @return \Illuminate\Http\Response
     */
    public function destroy($reviewID)
    {
        $review = Review::findOrFail($reviewID);
        $review->delete();
        return redirect($this->page)->with('success', 'Review deleted');
    }

    private function validatedData(Request $request)
    {
        return $request->validate([
            'name' => 'required|max:191',
            'message' => 'required|max:1000',
            // 'rating' => 'required|numeric|min:1|max:5',
            'status' => 'required',
        ]);
    }
}
