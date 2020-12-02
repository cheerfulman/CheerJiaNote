<?php

namespace App\Models\comment;

use Illuminate\Database\Eloquent\Model;
/**
 * 评论
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property int $created_at
 * @property int $updated_at
 */
class comment extends Model
{
    //
    protected $primaryKey = 'id';

    protected $connection = 'mysql';

    protected $table = 'comment';

    protected $dateFormat = 'U';
}
