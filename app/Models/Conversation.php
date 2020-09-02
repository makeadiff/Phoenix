<?php
namespace App\Models;

use App\Models\Common;

final class Conversation extends Common
{
    protected $table = 'Conversation';
    public $timestamps = true;
    const CREATED_AT = 'added_on';
    const UPDATED_AT = 'updated_on';
    protected $fillable = ['user_id','assigned_to_user_id','type','stage','scheduled_on','comment','followup_to_conversation_id','added_by_user_id'];

    public function added_by_user()
    {
        return $this->hasOne("App\Models\User", 'id', "added_by_user_id");
    }

    public function assigned_to()
    {
        return $this->hasOne("App\Models\User", 'id', "assigned_to_user_id");
    }

    public function user()
    {
        return $this->hasOne("App\Models\User", 'id', "user_id");
    }

    public function parent_conversation()
    {
        return $this->hasOne("App\Models\Conversation", 'id', "followup_to_conversation_id");
    }

    public static function search($data)
    {
        $search_fields = ['id', 'user_id','assigned_to_user_id', 'city_id', 'center_id', 'group_id', 'vertical_id', 'comment'];
        $q = app('db')->table('Conversation')->distinct();
        $q->select('Conversation.id','Conversation.user_id','Conversation.assigned_to_user_id','Conversation.type',
                    'Conversation.stage','Conversation.scheduled_on','Conversation.comment','Conversation.followup_to_conversation_id', 'Conversation.added_on');

        foreach ($search_fields as $field) {
            if (empty($data[$field])) {
                continue;
            }
            if ($field === 'comment') {
                $q->whereLike($field, "%" . $data[$field] . "%");

            } elseif ($field === 'city_id') {
                $q->join("User", 'User.id', '=', 'Conversation.user_id');
                $q->where("User.city_id", $data[$field]);

            // } elseif ($field === 'center_id') { // We'll wait for User.center_id implementation to do this.
            } elseif ($field === 'group_id') {
                $q->join("UserGroup", 'UserGroup.user_id', "=", 'Conversation.user_id');
                $q->where("UserGroup.group_id", $data[$field]);

            } elseif ($field === 'vertical_id') {
                $q->join("UserGroup", 'UserGroup.user_id', "=", 'Conversation.user_id');
                $q->join("Group", 'UserGroup.group_id', "=", 'Group.id');
                $q->where("Group.vertical_id", $data[$field]);

            } else {
                $q->where($field, $data[$field]);
            }
        }
        $results = $q->get();

        return $results;
    }

    public function remove()
    {
        list($conversation_id) = func_get_args();
        return $this->destroy($conversation_id);
    }

    public function add($data)
    {
        $conversation = [
            'user_id'       => $data['user_id'],
            'assigned_to_user_id' => $data['assigned_to_user_id'], 
            'type'          => $data['type'],
            'stage'         => !empty($data['stage']) ? $data['stage'] : 'scheduled',
            'scheduled_on'  => !empty($data['scheduled_on']) ? date('Y-m-d', strtotime($data['scheduled_on'])) : null,
            'comment'       => !empty($data['comment']) ? $data['comment'] : '',
            'followup_to_conversation_id'   => !empty($data['followup_to_conversation_id']) ? !empty($data['followup_to_conversation_id']) : 0,
            'added_by_user_id' => !empty($data['added_by_user_id']) ? $data['added_by_user_id'] : 0
        ];

        $conversation = $this->create($conversation);

        return $conversation;
    }
}
