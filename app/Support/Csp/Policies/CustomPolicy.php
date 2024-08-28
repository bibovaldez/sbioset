<?php

namespace App\Support\Csp\Policies;

use Spatie\Csp\Policies\Basic;
use Spatie\Csp\Directive;
use Spatie\Csp\Policies\Policy;

class Custompolicy extends Basic
{
    public function configure()
    {
        parent::configure();

        $this
    ->addDirective('default-src', ['self'])
    ->addDirective('script-src', [
        'self',
        'https://cdn.jsdelivr.net',
        'https://www.google.com',
        'https://www.gstatic.com',
        'https://cdnjs.cloudflare.com',  // Added for Font Awesome
        'unsafe-inline',  // May be required for some inline scripts
        'unsafe-eval'     // May be required for some dynamic scripts
    ])
    ->addDirective('style-src', [
        'self',
        'unsafe-inline',
        'https://fonts.bunny.net',
        'https://cdnjs.cloudflare.com'
    ])
    ->addDirective('font-src', [
        'self',
        'https://fonts.bunny.net',
        'https://cdnjs.cloudflare.com'
    ])
    ->addDirective('img-src', [
        'self',
        'data:',
        'https:'
    ])
    ->addDirective('connect-src', [
        'self',
        'https://fonts.bunny.net',
        'wss:',  // Added for WebSocket connections if needed
        'https://www.google.com'  // Added for reCAPTCHA
    ])
    ->addDirective('frame-src', [
        'self',
        'https://www.google.com'  // Added for reCAPTCHA iframe
    ]);
    }
}
