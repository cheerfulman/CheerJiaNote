<?php
/**
 * Created by Futuweb/增长与基础架构组
 * User: kevenzheng
 * Date: 2019-08-27
 * Time: 19:16
 */

namespace App\Service\Example;


use App\Enums\ExceptionCode;
use App\Enums\MonitorId;
use App\Exceptions\HttpResponseException;
use App\Facades\Logger;
use App\Facades\Reporter;

class Exception extends HttpResponseException
{
    /**
     * @param $data
     *
     * @return Exception
     */
    public static function businessFail($data): Exception
    {
        $message = '这是一个异常，返回内容由你决定';
        Reporter::report(MonitorId::EXAMPLE_ID);
        Logger::notice($message . json_encode($data));
        return new self($message, ExceptionCode::EXAMPLE_BUSINESS_FAIL);
    }
}
