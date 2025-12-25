<?php

namespace App\Http\Controllers;

use App\Support\ApiResponse;

abstract class BaseApiController extends Controller
{
    use ApiResponse;
}
