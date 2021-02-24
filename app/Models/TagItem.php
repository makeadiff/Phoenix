<?php
namespace App\Models;

use App\Models\Common;

final class TagItem extends Common
{
    protected $table = 'TagItem';
    public $timestamps = true;
    const CREATED_AT = 'added_on';
    const UPDATED_AT = false;
    protected $fillable = ['item_type', 'item_id', 'tag_id'];

    public function items()
    {
        return $this->morphTo();
    }
    
}