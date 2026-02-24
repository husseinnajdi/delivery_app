<?php

namespace App\Http\Controllers;

use App\Models\currencies as currency;
class Currencies extends Controller
{
    public function index(){
        return currency::all();
    }
}
