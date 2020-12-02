<?php
/**
 * Created by Futuweb/增长与基础架构组
 * User: kevenzheng
 * Date: 2019-08-27
 * Time: 19:22
 */

namespace App\Exceptions;


use Illuminate\Http\Exceptions\HttpResponseException as BaseException;

class HttpResponseException extends BaseException
{
    public function __construct(string $message, int $code, array $data = [])
    {
        parent::__construct(response(compact('code', 'message', 'data')));
    }
}
