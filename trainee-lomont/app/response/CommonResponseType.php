<?php


namespace App\response;


class CommonResponseType
{
    public static function success($date){
        return response(['code' => 0, 'message' => '', 'date' => $date]);
    }

    public static function fail($code,$message){
        return response(['code' => $code, 'message' => $message,'date' => []]);
    }
}