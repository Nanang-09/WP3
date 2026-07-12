<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\MathCaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login', [
            'captcha' => MathCaptcha::generate(),
        ]);
    }

    /**
     * Show the application registration form.
     */
    public function register()
    {
        return view('auth.register', [
            'captcha' => MathCaptcha::generate(),
        ]);
    }

    /**
     * Handle an incoming registration request.
     */
    public function storeRegister(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', 'min:8'],
            'captcha_answer' => ['required', 'integer'],
        ], [
            'captcha_answer.required' => 'Jawaban captcha wajib diisi.',
        ]);

        if (! MathCaptcha::validate($request->input('captcha_answer'))) {
            throw ValidationException::withMessages([
                'captcha_answer' => 'Jawaban captcha salah. Silakan coba lagi.',
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_CUSTOMER,
        ]);

        Auth::login($user);

        return redirect()->intended(route('home'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'captcha_answer' => ['required', 'integer'],
        ], [
            'captcha_answer.required' => 'Jawaban captcha wajib diisi.',
        ]);

        if (! MathCaptcha::validate($request->input('captcha_answer'))) {
            throw ValidationException::withMessages([
                'captcha_answer' => 'Jawaban captcha salah. Silakan coba lagi.',
            ]);
        }

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            return redirect()->intended($this->homeRouteFor(auth()->user()));
        }

        throw ValidationException::withMessages([
            'email' => 'Email atau password salah.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public static function homeRouteFor(User $user): string
    {
        if ($user->isAdmin()) {
            return route('admin.dashboard');
        }

        if ($user->isForeman()) {
            return route('foreman.dashboard');
        }

        return route('home');
    }
}
