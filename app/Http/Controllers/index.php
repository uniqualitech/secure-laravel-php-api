<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class test_api extends Controller
{
   use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;
}
