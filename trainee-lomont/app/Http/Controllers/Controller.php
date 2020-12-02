<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function success($date){
        return response(['code' => 0, 'message' => '', 'date' => $date]);
    }

    public function fail($code,$message){
        return response(['code' => $code, 'message' => $message,'date' => []]);
    }
}
