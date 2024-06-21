<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;
class PageAddons extends Controller
{
    public function faq(Request $request){

        return view('faq');
    }
    public function getVisits()
    {
        $visits = Visit::all();
        return response()->json($visits);
    }
}

