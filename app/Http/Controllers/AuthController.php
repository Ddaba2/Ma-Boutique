<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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

        // Chercher l'utilisateur par email ou nom
        $user = User::where('email', $credentials['username'])
                    ->orWhere('name', $credentials['username'])
                    ->first();

        if ($user && !$user->active) {
            return back()->withErrors(['username' => 'Votre compte est désactivé. Contactez le gérant.'])->onlyInput('username');
        }

        if (Auth::attempt(['email' => $credentials['username'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'Connexion réussie !');
        }

        if (Auth::attempt(['name' => $credentials['username'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'Connexion réussie !');
        }

        return back()->withErrors([
            'username' => 'Les identifiants fournis ne correspondent pas.',
        ])->onlyInput('username');
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
