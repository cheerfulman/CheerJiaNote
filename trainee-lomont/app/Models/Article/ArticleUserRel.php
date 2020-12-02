<?php

namespace App\Models\Article;

use Illuminate\Database\Eloquent\Model;


/**
 * 文章与用户关系
 *
 * @property int $id
 * @property int $article_id
 * @property int $uid
 * @property int $created_at
 * @property int $updated_at
 */
class ArticleUserRel extends Model
{
    //
    protected $primaryKey = 'id';

    protected $connection = 'mysql';

    protected $table = 'article_user_rels';

    protected $dateFormat = 'U';

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';
}
