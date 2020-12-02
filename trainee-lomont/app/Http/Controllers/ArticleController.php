<?php


namespace App\Http\Controllers;

use App\Article;
use App\Enums\ErrorCode;
use App\response\CommonResponseType;
use App\Services\Example\ArticleService;
use http\Header;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * 文章的发布
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Symfony\Component\HttpFoundation\Response
     */
    public function articlePost(Request $request){

        // 通过ShareErrorsFromSession 将其放入error放入session中
//        $this->validate($request,[
//            'title' => 'required|max:255',
//            'content' => 'required'
//
        $title = $request->input('title');
        $content = $request->input('content');
        if(!ArticleService::checkTitleAndContent($title,$content)){
            return $this->fail(ErrorCode::ARTICLE_TITLE_OR_CONTENT_EMPTY,'请填写完成标题和内容');
        }

        $poster_url = $request->input('poster_url','');
        $uid = $request->header('x-futu-client-nnid');
        if(empty($uid)){
            return $this->fail(ErrorCode::ARTICLE_UID_EMPTY,'信息不全');
        }

        $article_id = ArticleService::articlePost($uid,$title,$content,$poster_url);

        return $this->success(['article_id' => $article_id]);
    }

    /**
     * 点赞功能
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Symfony\Component\HttpFoundation\Response
     */
    public function likeArticle(Request $request){
        // 最好还要去查用户表 看此用户是否存在来进行风控校验（这里没user id，所以直接拿 header中的uid当用户id一直使用） ----  最好实现风控校验与业务代码的解耦
        $uid = $request->header('x-futu-client-nnid');
        if(empty($uid)){
            return $this->fail(ErrorCode::ARTICLE_UID_EMPTY,'信息不全');
        }
        $articleId = intval($request->input('article_id'));
        if(empty($articleId) || $articleId < 0){
            return $this->fail(ErrorCode::ARTICLE_ARTICLE_ID_EMPTY,'文章不存在');
        }
//        echo $uid . ' ' . $articleId;

        $data = ArticleService::articleLike($uid,$articleId);
        return $this->success($data);
    }

    /**
     * 取消点赞
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Symfony\Component\HttpFoundation\Response
     */
    public function unlikeArticle(Request $request){
        $uid = $request->header('x-futu-client-nnid');
        if(empty($uid)){
            return $this->fail(ErrorCode::ARTICLE_UID_EMPTY,'信息不全');
        }
        $articleId = intval($request->input('article_id'));
        if(empty($articleId) || $articleId < 0){
            return $this->fail(ErrorCode::ARTICLE_ARTICLE_ID_EMPTY,'文章不存在');
        }

        $data = ArticleService::articleUnLike($uid,$articleId);
        return $this->success($data);
    }

    /**
     * 展示列表
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Symfony\Component\HttpFoundation\Response
     */
    public function showList(Request $request){
        $size = $request->input('size');
        $page = $request->input('page');
        $status = $request->input('status');
        $uid = $request->header('x-futu-client-nnid');
        if(empty($uid)){
            $uid = -1;
        }
        return  ArticleService::articleShowList($size,$page,$status,$uid);
    }

    /**
     * 审核功能，更改status状态
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Symfony\Component\HttpFoundation\Response
     */
    public function actionCensor(Request $request){
        $article_id = $request->input('article_id');
        $status = $request->input('status');

        $uid = $request->header('x-futu-client-nnid');
        if(empty($uid)){
            return $this->fail(ErrorCode::ARTICLE_UID_EMPTY,'信息不全');
        }
        // 不能对外暴露太多异常信息，故沿用 uid errorCode
        $sid = $request->header('x-futu-client-staffid');
        if(empty($sid)){
            return $this->fail(ErrorCode::ARTICLE_UID_EMPTY, '信息不全');
        }
        ArticleService::articleCensor($article_id,$status,$uid);
        return $this->success([]);
    }
}
