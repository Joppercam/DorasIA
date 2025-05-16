<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

<<<<<<< HEAD
abstract class Controller extends BaseController
=======
class Controller extends BaseController
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
{
    use AuthorizesRequests, ValidatesRequests;
}
