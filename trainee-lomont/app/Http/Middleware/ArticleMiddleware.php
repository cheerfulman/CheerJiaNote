<?php
/**
 * Created by echolu
 * Date: 2019-11-19
 * Time: 11:53
 */
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**article服务标例中间件
 * Class ArticleMiddleware
 * @package App\Http\Middleware
 */
class ArticleMiddleware
{
    public function handle(Request $request, Closure $next){
        /**
         * todo action执行前的一些操作
         */

        $response = $next($request);

        /**
         * todo action执行后的一些操作
         */

        return $response;
    }
}