<?php

namespace Tests\Feature;

use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Tests\TestCase;

class SocialAuthControllerTest extends TestCase
{
    public function test_google_redirect_returns_to_login_when_credentials_are_missing(): void
    {
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
            'services.google.redirect' => '/auth/google/callback',
        ]);

        $response = $this->from(route('login'))->get(route('social.redirect', 'google'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors([
            'email' => 'Login dengan Google belum dikonfigurasi. Isi GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET di file .env, lalu jalankan php artisan config:clear.',
        ]);
    }

    public function test_google_redirect_uses_socialite_when_credentials_are_present(): void
    {
        config([
            'services.google.client_id' => 'google-client-id',
            'services.google.client_secret' => 'google-client-secret',
            'services.google.redirect' => '/auth/google/callback',
        ]);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturnSelf();
        Socialite::shouldReceive('redirect')
            ->once()
            ->andReturn(new RedirectResponse('https://accounts.google.com/o/oauth2/auth'));

        $response = $this->get(route('social.redirect', 'google'));

        $response->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    public function test_login_and_register_pages_always_show_google_button(): void
    {
        $loginResponse = $this->get(route('login'));
        $loginResponse->assertOk();
        $loginResponse->assertSee(route('social.redirect', 'google'));

        $registerResponse = $this->get(route('register'));
        $registerResponse->assertOk();
        $registerResponse->assertSee(route('social.redirect', 'google'));
    }
}
