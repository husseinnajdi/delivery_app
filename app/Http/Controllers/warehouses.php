<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\warehouses as warehouse;
class warehouses extends Controller
{
    public function index(){
        return warehouse::all();
    }

    public function show($id){
        return warehouse::find($id);
    }

    public function store(Request $request){
        $warehouse = warehouse::create($request->all());
        return response()->json($warehouse, 201);
    }

    public function update(Request $request, $id){
        $warehouse = warehouse::findOrFail($id);
        $warehouse->update($request->all());
        return response()->json($warehouse, 200);
    }
}
