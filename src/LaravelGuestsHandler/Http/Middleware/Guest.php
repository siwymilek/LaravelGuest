<?php

namespace Siwymilek\LaravelGuestsHandler\Http\Middleware;

use Closure;
use Siwymilek\LaravelGuestsHandler\Guest as GuestHandler;

class Guest
{
    protected $guest;

    /**
     * Create a new filter instance.
     *
     * @param  Guest  $guest
     * @return void
     */
    public function __construct(GuestHandler $guest)
    {
        $this->guest = $guest;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
