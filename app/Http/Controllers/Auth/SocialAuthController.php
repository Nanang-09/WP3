<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    private const SUPPORTED_PROVIDERS = ['google'];

    /**
     * Redirect the user to the provider authentication page.
     */
    public function redirect($provider)
    {
        abort_unless($this->isSupportedProvider($provider), 404);

        if (! $this->isProviderConfigured($provider)) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => $this->missingConfigMessage($provider)]);
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from provider.
     */
    public function callback($provider)
    {
        abort_unless($this->isSupportedProvider($provider), 404);

        if (! $this->isProviderConfigured($provider)) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => $this->missingConfigMessage($provider)]);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Social Login Error for ' . $provider . ': ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->route('login')->withErrors(['email' => 'Gagal login melalui '.ucfirst($provider).'. Silakan coba lagi.']);
        }

        if (! $socialUser->getEmail()) {
            return redirect()->route('login')->withErrors(['email' => 'Akun '.ucfirst($provider).' Anda tidak mengembalikan email. Gunakan provider lain atau login manual.']);
        }

        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            $user->update([
                'provider_name' => $provider,
                'provider_id' => $socialUser->getId(),
                'provider_token' => $socialUser->token,
            ]);
        } else {
            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                'email' => $socialUser->getEmail(),
                'password' => Hash::make(Str::random(32)),
                'role' => User::ROLE_CUSTOMER,
                'provider_name' => $provider,
                'provider_id' => $socialUser->getId(),
                'provider_token' => $socialUser->token,
            ]);
        }

        Auth::login($user, remember: true);

        return redirect()->intended(AuthController::homeRouteFor($user))
            ->with('success', $user->wasRecentlyCreated
                ? 'Akun Google berhasil dibuat. Selamat datang!'
                : 'Berhasil masuk dengan Google.');
    }

    private function isSupportedProvider(string $provider): bool
    {
        return in_array($provider, self::SUPPORTED_PROVIDERS, true);
    }

    private function isProviderConfigured(string $provider): bool
    {
        return filled(config("services.{$provider}.client_id"))
            && filled(config("services.{$provider}.client_secret"))
            && filled(config("services.{$provider}.redirect"));
    }

    private function missingConfigMessage(string $provider): string
    {
        $providerKey = strtoupper($provider);

        return 'Login dengan '.ucfirst($provider).' belum dikonfigurasi. Isi '
            .$providerKey.'_CLIENT_ID dan '.$providerKey.'_CLIENT_SECRET di file .env, lalu jalankan php artisan config:clear.';
    }
}
