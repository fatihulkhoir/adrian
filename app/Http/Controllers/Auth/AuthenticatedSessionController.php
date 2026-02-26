<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Proses login berdasarkan email dan password.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => 'required|email',
            'password' => 'required|string|min:4',
        ]);

        $user = User::where('email', $request->login)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'login' => 'Login gagal. Periksa kembali email dan password.',
            ])->onlyInput('login');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/redirect');
    }

    /**
     * Logout user dan akhiri sesi.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}