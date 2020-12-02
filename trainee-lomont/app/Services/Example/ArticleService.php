<?php


namespace App\Services\Example;


use App\Enums\ErrorCode;
use App\Models\Article\Article;

use App\Models\Article\ArticleUserRel;
use App\Service\Article\ArticleException;

class ArticleService
{
    // 上传文章
    public static function articlePost($uid,$title,$content,$post_url = ''){
        $article = new Article();
        $article->uid = $uid;
        $article->title = $title;
        $article->content = $content;
        $article->poster_url = $post_url;

        try{
            $article->save();
        }catch (\Exception $e){
            echo $e->getMessage();
        }
        return $article->id;
    }
    // 检测标题和内容不能为空
    public static function checkTitleAndContent($title,$content){
        if(!empty($title) && !empty($content)){
            return true;
        }
        return false;
    }

    // 点赞
    public static function articleLike($uid,$articleId){
        // 查询文章id如果不存在肯定是无法点赞的
        $article = Article::query()->find($articleId);
        if($article == null || !$article instanceof Article){
            throw new ArticleException('文章不存在', ErrorCode::ARTICLE_EMPTY);
        }
        // 分两种情况，改用户点过还是未点过
        if(self::articleLikeStatus($uid,$articleId)){
            // 点过则返回 is_like = 1 点赞成功 保存到数据库
                $articleUserRel = new ArticleUserRel();
                $articleUserRel->uid = $uid;
                $articleUserRel->article_id = $articleId;
                $articleUserRel->save();
            // 获取该文章的点赞数 并返回
            return ['is_like' => '1','like_number' => self::articleLikeNumber($articleId)];
        }
        // 接口文档要分开，一时没看见，索性分开；

//        else{
//            // 否则删除数据库中 改用户id的点赞信息，并且返回 is_like = 0
//            ArticleUserRel::where(['article_id'=>$articleId, 'uid' => $uid])->delete();
//            return ['is_like' => '0','like_number' => self::articleLikeNumber($articleId)];
//        }
    }

    public static function articleUnLike($uid,$articleId){
        // 查询文章id如果不存在肯定是无法点赞的
        $article = Article::query()->find($articleId);
        if($article == null || !$article instanceof Article){
            throw new ArticleException('文章不存在', ErrorCode::ARTICLE_EMPTY);
        }
        if(!self::articleLikeStatus($uid,$articleId)) {
            ArticleUserRel::where(['article_id'=>$articleId, 'uid' => $uid])->delete();
            return ['is_like' => '0','like_number' => self::articleLikeNumber($articleId)];
        }
    }

    // 查询点赞数
    public static function articleLikeNumber($articleId){
       return ArticleUserRel::query()->where(['article_id'=>$articleId])->count();
    }
    // 用户是否点赞该文章
    public static function articleLikeStatus($uid,$articleId){
        $count = ArticleUserRel::query()->where(['article_id'=>$articleId, 'uid' => $uid])->count();
        if($count <= 0){
            return true;
        }else {
            return false;
        }
    }

    // 展示文章列表
    public static function articleShowList($size,$page,$status,$uid){
        // 增加代码鲁棒性
        self::CheckSizePageStatus($size,$page,$status);
        $query = Article::query();
        if(isset($status)){
            $query->where(['status'=>intval($status)]);
        }
        $total = $query->count();

        // 校验 page 的合理性
        if($page > $total / size){
            throw new ArticleException('超出页面限制',ErrorCode::ARTICLE_PAGE_ERROR);
        }

        // 返回的list 数据
        $articleList = [];

        // 或者当前页的所有除 （是否点赞和点赞总数的 数据）
        $allArticle = Article::query()
            ->where('status',$status)
            ->orderBy('updated_at','desc')
            ->offset(($page - 1) *  $size)
            ->limit($size)
            ->get();
        if(!empty($allArticle)){
            $articleList = $allArticle->toArray();
            // 所有文章的id 存入 $articleTotalId
            $articleTotalId = [];
            foreach ($allArticle as $article){
                $articleTotalId[] = $article['id'];
            }

            // SELECT article_id,count(1) FROM article_user_rels GROUP BY article_id;
            $ArticleLikeNumber = ArticleUserRel::query()
                ->selectRaw('article_id, count(*) as likeNumber')
                ->whereIn('article_id',$articleTotalId)
                ->groupBy('article_id')
                ->get()->toArray();
            // 定义 id 与 点赞数的映射 从 a[0] = ['id',likeNumber] ---> Map['id'] = likeNumber
            $idLikeNumMap = [];
            if(!empty($ArticleLikeNumber)){
                foreach ($ArticleLikeNumber as $aln){
                    $idLikeNumMap[$aln['article_id']] = $aln['likeNumber'];
                }
            }

            // 已登录， uid不为 -1
            if(!empty($uid) && $uid != -1){
                // 把自己点赞的article_id拿出来即可
                foreach ($articleTotalId as $article_id){
                    $myLikeArticle = ArticleUserRel::query()->where(['article_id'=>$article_id,'uid'=>$uid])->get()->toArray();
                }
            }
            // 将like_number 和 is_like 放入articleList数组中
            foreach ($articleList as &$article){
                $article['like_number'] = 0;
                if(!empty($idLikeNumMap) && isset($idLikeNumMap[$article['id']])){
                    $article['like_number'] = $idLikeNumMap[$article['id']];
                }

                $article['is_like'] = 0;
                if(!empty($myLikeArticle) && isset($myLikeArticle[$article['id']])){
                    $article['id_like'] = 1;
                }
            }
        }
        return [
            'list' => $articleList,
            'pages' => [
                'page' => $page,
                'size' => $size,
                'total_count' => $total
            ]
        ];

    }
    // 参数校验 增加鲁棒性
    public static function CheckSizePageStatus(&$size,&$page,&$status){
        if(!isset($size)){
            $size = 10;
        }
        if(!isset($page) || $page <= 0){
            $page = 1;
        }
        if(!isset($status)) {
            $status = 0;
        }
    }

    // 修改文章status
    public static function articleCensor($article_id,$status,$uid){
        $article = Article::find($article_id);
        if($article instanceof Article){
            $article->status = $status;
            $article->censor_sid = $uid;
            if($article->save()){
                return true;
            }
        }else{
            throw new ArticleException('文章不存在',ErrorCode::ARTICLE_EMPTY);
        }
    }


}