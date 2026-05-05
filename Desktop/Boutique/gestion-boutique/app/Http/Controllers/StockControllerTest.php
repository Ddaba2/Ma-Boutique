<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StockControllerTest extends Controller
{
    public function index()
    {
        return view('stocks.index');
    }
}
