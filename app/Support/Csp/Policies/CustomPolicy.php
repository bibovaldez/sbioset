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
            ->addDirective('default-src', 'self')
            ->addDirective('script-src', [
                'self',
                'https://cdn.jsdelivr.net',
                'https://www.google.com',
                'https://www.gstatic.com',  // Required for reCAPTCHA
            ])
            ->addDirective('style-src', [
                'self',
                'unsafe-inline',  // Required for Livewire
                'https://fonts.bunny.net',
                'https://cdnjs.cloudflare.com',
            ])
            ->addDirective('font-src', [
                'self',
                'https://fonts.bunny.net',
                'https://cdnjs.cloudflare.com',
            ])
            ->addDirective('img-src', [
                'self',
                'data:',
                'https:',  // Allows loading images from any HTTPS source
            ])
            ->addDirective('connect-src', [
                'self',
                'https://fonts.bunny.net',
            ]);
    }
}
