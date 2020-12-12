<?php
namespace App\Models;

use App\Models\Common;

final class Tag extends Common
{
    protected $table = 'Tag';
    public $timestamps = true;
    const CREATED_AT = 'added_on';
    const UPDATED_AT = false;
    protected $fillable = ['name'];

    private $all_item_types = ['User','Student','City','Center','Event','Comment','Class','Batch','Level'];

    public function fetch($tag)
    {
        $tag = $this->where('name', $tag)->first();

        if($tag) return $this->find($tag->id);
        return null;
    }

    public function item()
    {
        $this->morphTo();
    }

    public function items($item)
    {
        if(in_array($item, $this->all_item_types)) {
            $table = ucfirst($item);
        } else {
            $this->error("Given Item type $item is not a valid choice.");
            return null;
        }

        return $this->morphedByMany("App\Models\\$table", 'item', 'TagItem');
    }
    // A few aliases...
    public function users() { return $this->items('User'); }
    public function students() { return $this->items('Student'); }
    public function centers() { return $this->items('Center'); }
    public function events() { return $this->items('Event'); }
    public function comments() { return $this->items('Comment'); }
    public function classes() { return $this->items('Class'); }

    public static function search($data)
    {
        $search_fields = ['tag', 'item_type','item_id'];
        $q = app('db')->table('ItemTag');
        $q->join("Tag", "ItemTag.tag_id", '=', 'Tag.id');
        $q->select('id', 'item_type', 'item_id', 'added_on');

        foreach ($search_fields as $field) {
            if (empty($data[$field])) {
                continue;
            }
            if ($field === 'tag') {
                $q->where("Tag.name", $data[$field]);
            } else {
                $q->where("ItemTag." . $field, $data[$field]);
            }
        }
        $results = $q->get();

        return $results;
    }

    public function findTags($item_type, $item_id)
    {
        $q = app('db')->table('ItemTag');
        $q->join("Tag", "ItemTag.tag_id", '=', 'Tag.id');
        $q->where('item_type', $item_type)->where('item_id', $item_id);

        return $q->result()->pluck('name');
    }
    public function isTagged($item_type, $item_id, $tag, $tag_id = 0)
    {
        if(!$tag_id) {
            $tag_id = $this->addTag($tag);
        }
        return app('db')->table('TagItem')->where('item_type',$item_type)->where('item_id',$item_id)->where('tag_id',$tag_id)->get();
    }

    public function untag($item_type, $item_id, $tag)
    {
        $tag_row = app('db')->table('Tag')->where('name', $tag)->first();
        if(!$tag_row) return false;

        return $this->where('item_type',$item_type)->where('item_id', $item_id)->where('tag_id', $tag_row->id);
    }

    public function tagItem($item_type, $item_id, $tag)
    {
        $tag_id = $this->addTag($tag);

        if(!is_array($item_id)) { // If second parameter is not an array, convert it to an array.
            $item_id = [$item_id];
        }

        $insert_count = 0;
        foreach($item_id as $iid) {
            $tagged = $this->isTagged($item_type, $iid, $tag, $tag_id);
            if(!$tagged->count()) { // It not already tagged, add the tagging.
                app('db')->table('TagItem')->insert([
                    'item_type' => $item_type,
                    'item_id'   => $iid,
                    'tag_id'    => $tag_id,
                    'added_on'  => date('Y-m-d H:i:s')
                ]);
                $insert_count++;
            }
        }

        return $insert_count;
    }

    public function addTag($tag)
    {
        $tag_row = app('db')->table('Tag')->where('name', $tag)->first();
        if($tag_row) return $tag_row->id;

        $tag_id = app('db')->table('Tag')->insertGetId([
            'name'      => $tag,
            'added_on'  => date('Y-m-d H:i:s')
        ]);

        return $tag_id;
    }
}
