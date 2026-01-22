<?php

use App\Http\Controllers\Controller;
use App\Models\Shop;
class ShopController extends Controller{
public function index(){
    return Shop::all();
}
public function store(Request $request){
    Shop::create([
        'name' => $request->name,
        'adress' => $request->adress,
        'phone' => $request->phone,
        'city' => $request->city,
        'user_id' => $request->user_id,
    ]);
    return response()->json(['message' => 'Shop created successfully'], 201);
}
public function update(Request $request, $id){
    $shop = Shop::find($id);
    if (!$shop) {
        return response()->json(['message' => 'Shop not found'], 404);
    }
    $shop->update($request->only(['name', 'adress', 'email', 'phone', 'city', 'user_id']));
    return response()->json(['message' => 'Shop updated successfully']);
}
public function show($id){
    $shop = Shop::find($id);
    if (!$shop) {
        return response()->json(['message' => 'Shop not found'], 404);
    }
    return response()->json($shop);
}
}