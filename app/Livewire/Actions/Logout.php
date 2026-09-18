<?php

namespace App\Livewire\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke(?Request $request = null)
    {
        $request ??= request();
        $redirectToGroomerSpace = Auth::guard('groomer_spacer')->check() ||
            $request->input('redirect') === 'login-groomer-space';

        Auth::guard('web')->logout();
        Auth::guard('groomer_spacer')->logout();

        Session::invalidate();
        Session::regenerateToken();

        if ($redirectToGroomerSpace) {
            return redirect('/login-groomer-space');
        }

        return redirect('/');
    }
}
