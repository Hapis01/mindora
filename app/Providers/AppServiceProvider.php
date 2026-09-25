<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Services\AI\AIProviderInterface::class,
            function () {
                $provider = config('services.ai_provider', 'gemini');
                if ($provider === 'deepseek') {
                    return new \App\Services\AI\DeepSeekProvider();
                }
                return new \App\Services\AI\GeminiProvider();
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ── Named Rate Limiters ────────────────────────────────────────────────
        // 'login'        : 5 attempts per minute per IP
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // 'register'     : 10 attempts per minute per IP
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // 'ai-message'   : 15 messages per minute per authenticated user
        RateLimiter::for('ai-message', function (Request $request) {
            return Limit::perMinute(15)->by(optional($request->user())->id ?: $request->ip());
        });

        // 'assessment'   : 60 requests per minute (answer saves etc.)
        RateLimiter::for('assessment', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
