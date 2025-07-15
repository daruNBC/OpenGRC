<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SessionTimeout
{
    public function handle(Request $request, Closure $next)
    {

        if (!Auth::check()) {
            return $next($request);
        }

        $timeout = setting('security.session_timeout', 15) * 60;
        $user = auth()->user();

        // Get current last_activity directly from database
        $currentActivity = DB::table('users')
            ->where('id', $user->id)
            ->value('last_activity');      

        // If the user has been inactive for longer than the timeout, log them out.
        if ($currentActivity && strtotime($currentActivity) + $timeout < now()->timestamp) {
            \Filament\Facades\Filament::auth()->logout();
            return redirect()->route('filament.app.auth.login');
        }

        return $next($request);
    }
}
