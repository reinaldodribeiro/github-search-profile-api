<?php

namespace App\Http\Controllers;

use App\Services\CrudService;
use Illuminate\Http\Request;

class CrudController extends Controller
{
    /**
     * @var CrudService
     */
    protected $service;

    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request
     * @return object
     */
    public function index(Request $request) {
        $payload = $this->service->list($this->prepareFilters($request->all()));
        return response()->json($payload);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return object
     */
    public function store(Request $request) {
        return response()->json([
            'status' => true,
            'data' => $this->service->save($this->prepareData($request->all()))
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return object
     */
    public function show(Request $request, $id) {
        return response()->json([
            'status' => true,
            'data' => $this->service->search($id, $this->prepareFilters($request->all()))
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return object
     */
    public function update(Request $request, $id) {
        return response()->json([
            'status' => true,
            'data' => $this->service->update($id, $this->prepareData($request->all()))
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return object
     */
    public function destroy($id) {
        return response()->json([
            'status' => true,
            'data' => $this->service->destroy($id)
        ]);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareFilters($data) {
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareData($data) {
        return $data;
    }


}
