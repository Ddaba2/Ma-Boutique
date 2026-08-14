<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\BoutiqueContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where(function ($query) use ($credentials) {
            $query->where('email', $credentials['username'])
                ->orWhere('name', $credentials['username']);
        })->first();

        // Message identique pour compte inconnu, mot de passe incorrect ou compte
        // désactivé (évite l'énumération de comptes).
        if (! $user || ! $user->active || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'username' => 'Les identifiants fournis ne correspondent pas.',
            ])->onlyInput('username');
        }

        Auth::login($user);
        $request->session()->regenerate();
        $this->initialiserBoutique();

        return redirect()->intended(route('dashboard'))->with('success', 'Connexion réussie !');
    }

    private function initialiserBoutique(): void
    {
        $user = Auth::user();

        if ($user->boutique_id) {
            BoutiqueContext::definir($user->boutique_id);
        } else {
            session()->forget('boutique_id');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Déconnexion réussie.');
    }
}
