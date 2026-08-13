<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SauvegardeService;

class ParametreController extends Controller
{
    public function index(SauvegardeService $sauvegardes)
    {
        return view('parametres.index', [
            'utilisateurs' => User::with('boutique')->orderBy('name')->get(),
            'sauvegardes'  => $sauvegardes->lister(),
            'ongletActif'  => request('onglet', 'utilisateurs'),
        ]);
    }
}
