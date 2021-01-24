<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use stdClass;

class RegionController extends Controller
{
    private $table = 'regions';
    private $page = '/regions';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $regions = DB::table($this->table)->get();

        return view('regions.index', compact('regions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $region = new stdClass();
        $region->name = '';
        return view('regions.create', compact('region'));
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
        DB::table($this->table)->insert($data);
        return redirect($this->page)->with('success', 'Region saved');
    }

    /**
     * Display the specified resource.
     *
     * @param int
     * @return \Illuminate\Http\Response
     */
    public function show($region)
    {
        return view('regions.show', compact('region'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $region
     * @return \Illuminate\Http\Response
     */
    public function edit($region)
    {
        $region = DB::table($this->table)->where('id', $region)->first();
        if (!$region) {
            abort(404);
        }
        return view('regions.edit', compact('region'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $region
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $region)
    {
        $data = $this->validatedData($request);
        DB::table($this->table)->where('id', $region)->update($data);
        return redirect($this->page)->with('success', 'Region saved');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $region
     * @return \Illuminate\Http\Response
     */
    public function destroy($region)
    {
        DB::table($this->table)->where('id', $region)->delete();
        return redirect($this->page)->with('success', 'Region deleted');
    }

    private function validatedData(Request $request)
    {
        return $request->validate([
            'name' => 'required|max:191',
        ]);
    }
}
