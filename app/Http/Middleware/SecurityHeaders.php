<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SecurityHeaders Middleware
 *
 * Applies OWASP-recommended HTTP security headers to every response.
 * These headers instruct browsers to enforce security policies that
 * protect users against common attacks (XSS, clickjacking, MIME sniffing, etc.).
 */
class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Deny framing (prevent clickjacking)
        $response->headers->set('X-Frame-Options', 'DENY');

        // Force HTTPS in supported browsers (1 year, include subdomains)
        // NOTE: Only effective after deploying to HTTPS. Safe to set now.
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        // Enable XSS protection in older browsers
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Control referrer information sent to external sites
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy: restrict access to sensitive browser APIs
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=(), usb=()'
        );

        // Content Security Policy
        // - default-src 'self': allow resources from same origin only
        // - script-src 'self' 'unsafe-inline': needed for Vite-injected inline scripts
        // - style-src 'self' 'unsafe-inline' fonts.googleapis.com: allow Google Fonts CSS
        // - font-src 'self' fonts.gstatic.com data:: allow Google Fonts files
        // - connect-src 'self': allow XHR/fetch to same origin
        // - img-src 'self' data:: allow inline base64 images (charts/SVGs)
        $response->headers->set(
            'Content-Security-Policy',
            implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval'",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
                "font-src 'self' https://fonts.gstatic.com data:",
                "img-src 'self' data: https:",
                "connect-src 'self'",
                "frame-ancestors 'none'",
                "base-uri 'self'",
                "form-action 'self'",
            ])
        );

        // Remove potentially informative server header
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
