<?php
namespace App\Models;

use App\Models\Common;
use Illuminate\Database\Eloquent\Model;

final class Comment extends Model
{
    use Common;
    
    protected $table = 'Comment';
    public $timestamps = true;
    const CREATED_AT = 'added_on';
    const UPDATED_AT = false;
    protected $fillable = ['item_type', 'item_id', 'comment', 'added_by_user_id'];

    public function item()
    {
        return $this->morphTo();
    }

    public function tags()
    {
        return $this->belongsToMany("App\Models\Tag", 'TagItem', 'item_id', 'tag_id')->where('item_type', 'Comment');
    }

    public function added_by_user()
    {
        return $this->hasOne("App\Models\User", 'id', "added_by_user_id");
    }

    public static function search($data)
    {
        $search_fields = ['id', 'item_type','item_id', 'comment', 'added_by_user_id'];
        $q = app('db')->table('Comment');
        $q->select('id', 'item_type', 'item_id', 'comment', 'added_on', 'added_by_user_id');

        foreach ($search_fields as $field) {
            if (empty($data[$field])) {
                continue;
            }
            if ($field === 'comment') {
                $q->whereLike($field, "%" . $data[$field] . "%");
            } else {
                $q->where($field, $data[$field]);
            }
        }
        $results = $q->get();

        return $results;
    }

    public function findComment($item_type, $item_id)
    {
        $q = app('db')->table('Comment');
        $q->where('item_type', $item_type)->where('item_id', $item_id);

        $this->item = $q->first();
        return $this;
    }

    public function remove()
    {
        list($comment_id) = func_get_args();
        return $this->destroy($comment_id);
    }

    public function add($data)
    {
        if (empty($data['item_type']) or empty($data['item_id']) or empty($data['comment'])) {
            return false;
        }
        // Ideally this should be done using create() - but it was giving me a wierd issue that
        //      I was not able to fix. Possibly due to the polymorphic relationship. So, this.
        $comment_id = app('db')->table('Comment')->insertGetId([
            'item_type' => $data['item_type'],
            'item_id'   => $data['item_id'],
            'comment'   => $data['comment'],
            'added_by_user_id'  => isset($data['added_by_user_id']) ? $data['added_by_user_id'] : 0,
            'added_on'  => date('Y-m-d H:i:s')
        ]);

        $comment = $this->find($comment_id);

        return $comment;
    }
}
