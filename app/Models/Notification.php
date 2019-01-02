<?php
namespace App\Models;

use App\Models\Common;

final class Notification extends Common
{
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_on';
    protected $table = 'Push_Notification';
    public $timestamps = true;
    protected $fillable = ['user_id', 'imei_no', 'fcm_regid', 'status', 'platform', 'app'];

    public function search($data)
    {
        $q = app('db')->table('Push_Notification');

        $q->select('Push_Notification.id', 'Push_Notification.user_id', 'Push_Notification.imei_no', 'Push_Notification.fcm_regid', 'Push_Notification.platform', 'Push_Notification.app', 'Push_Notification.status', 
                    'Push_Notification.created_on', 'Push_Notification.updated_on',
                    'User.city_id','User.name','User.phone','User.email','User.mad_email');
        $q->join("User", "User.id", '=', 'Push_Notification.user_id');
        
        if(!isset($data['status'])) $data['status'] = '1';
        if($data['status'] !== false) $q->where('Push_Notification.status', $data['status']); // Setting status as '0' gets you even the disabled ones.

        if(!empty($data['user_id'])) $q->where('Push_Notification.user_id', $data['user_id']);
        if(!empty($data['imei'])) $q->where('Push_Notification.imei_no', $data['imei']);
        if(!empty($data['fcm_regid'])) $q->where('Push_Notification.fcm_regid', $data['fcm_regid']);
        if(!empty($data['platform'])) $q->where('Push_Notification.platform', $data['platform']);
        if(!empty($data['app'])) $q->where('Push_Notification.app', $data['app']);
        if(!empty($data['phone'])) $q->where('User.phone', $data['phone']);

        $q->orderBy('Push_Notification.created_on','desc');

        // dd($q->toSql(), $q->getBindings());
        $notifications = $q->get();
        
        return $notifications;
    }

    public function add($data)
    {
        $notification = Notification::create([
            'user_id'   => $data['user_id'],
            'imei_no'   => $data['imei'],
            'fcm_regid' => $data['fcm_regid'],
            'platform'  => $data['platform'],
            'app'       => $data['app'],
            'status'    => '1',
        ]);

        return $notification;
    }

}
