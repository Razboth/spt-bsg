<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menambahkan HTTP security headers pada setiap response.
 *
 * Mengatasi temuan VA (Tenable Nessus WAS):
 *  - 98056  Missing HTTP Strict Transport Security Policy
 *  - 98060  Missing 'X-Frame-Options' Header
 *  - 112529 Missing 'X-Content-Type-Options' Header
 *  - 112551 Missing Content Security Policy
 *  - 98526  Missing Permissions Policy
 *  - 98527  Missing Referrer Policy
 *
 * Header diset di level aplikasi supaya tetap ada meskipun TLS/HTTPS
 * di-terminate oleh reverse proxy / load balancer (F5 Big-IP) di depan aplikasi.
 */
class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = [
            // Mencegah clickjacking (98060)
            'X-Frame-Options' => 'SAMEORIGIN',

            // Mencegah MIME-sniffing (112529)
            'X-Content-Type-Options' => 'nosniff',

            // Membatasi informasi referrer (98527)
            'Referrer-Policy' => 'strict-origin-when-cross-origin',

            // Membatasi fitur browser (98526)
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=(), payment=(), usb=(), interest-cohort=()',

            // Content Security Policy (112551). Permisif agar tidak merusak
            // aset existing (Bootstrap, jQuery, Moment, inline script/style),
            // namun tetap membatasi object & meng-upgrade request HTTP.
            'Content-Security-Policy' => implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https: data:",
                "style-src 'self' 'unsafe-inline' https: data:",
                "img-src 'self' data: blob: https:",
                "font-src 'self' data: https:",
                "connect-src 'self' https:",
                "media-src 'self' https: data: blob:",
                "object-src 'none'",
                "frame-ancestors 'self'",
                "base-uri 'self'",
                "form-action 'self'",
                "upgrade-insecure-requests",
            ]),
        ];

        // HSTS (98056) hanya dikirim pada koneksi HTTPS, sesuai rekomendasi
        // agar tidak mengganggu akses HTTP murni saat development.
        if ($request->isSecure()) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        // Hilangkan header yang membocorkan informasi teknologi server.
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
