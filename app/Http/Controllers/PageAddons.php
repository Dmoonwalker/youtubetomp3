<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageAddons extends Controller
{
    public function faq(Request $request){

        return view('faq');
    }
}
