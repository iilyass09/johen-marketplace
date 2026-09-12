<?php

namespace App\Providers;

use App\Services\ThemeResolverService;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RedirectIfAuthenticated::redirectUsing(function () {
            if (Auth::guard('admin')->check()) {
                return route('admin.dashboard');
            }
            return route('home');
        });

        // Tema event untuk halaman publik marketplace. Diresolusi sekali per request.
        if (! $this->app->runningInConsole() && ! $this->isAdminRequest()) {
            $resolver = app(ThemeResolverService::class);
            $activeTheme = $resolver->resolveActiveTheme(request()->query('preview_theme'));

            if ($activeTheme) {
                $decorationUrls = array_map(fn ($img) => match (true) {
                    is_string($img) && str_starts_with($img, 'storage/') => asset($img),
                    is_string($img) => Storage::disk('public')->url($img),
                    default => null,
                }, (array) ($activeTheme->decorative_images ?: []));

                View::share([
                    'activeTheme' => $activeTheme,
                    'activeThemeCss' => ThemeResolverService::cssVars($activeTheme),
                    'activeDecorationImages' => array_values(array_filter($decorationUrls)),
                    'activeParticleEffect' => $activeTheme->particle_effect ?: null,
                    'activeThemeLogoUrl' => $activeTheme->logo_override
                        ? Storage::disk('public')->url($activeTheme->logo_override)
                        : null,
                ]);
            }
        }
    }

    protected function isAdminRequest(): bool
    {
        $path = request()->path();
        $routeName = request()->route()?->getName();

        return str_starts_with($path, 'admin')
            || str_starts_with($routeName ?? '', 'admin.');
    }
}