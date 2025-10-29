<?php

namespace App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckOnline
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = User::find(Auth::id());

            if ($user->last_seen && Carbon::parse($user->last_seen)->diffInMinutes(now()) >= 1) {
                if ($user->is_online) {
                    $user->update(['is_online' => false]);
                }
            } else {
                // Jika aktif lagi → set online
                if (! $user->is_online) {
                    $user->update(['is_online' => true]);
                }
            }
        }

        return $next($request);
    }
}
