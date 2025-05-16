<?php

// En app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Events\UserLoggedIn;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // Sobrescribir el método authenticated para disparar el evento
    protected function authenticated(Request $request, $user)
    {
        event(new UserLoggedIn($user));
    }
}
