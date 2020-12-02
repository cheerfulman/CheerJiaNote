<?php

namespace App\Models\Article;

use Illuminate\Database\Eloquent\Model;
/**
 * 文章
 *
 * @property int $id
 * @property int $uid
 * @property string $title
 * @property string $content
 * @property string $poster_url
 * @property int $status
 * @property int $censor_sid
 * @property int $created_at
 * @property int $updated_at
 */
class Article extends Model
{
    //
    protected $table = 'articles';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $dateFormat = 'U';

    protected $connection = 'mysql';


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
