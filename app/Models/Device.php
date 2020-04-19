<?php
namespace App\Models;

use App\Models\Common;

final class Device extends Common
{
    const CREATED_AT = 'added_on';
    const UPDATED_AT = 'updated_on';
    protected $table = 'Device';
    public $timestamps = true;
    protected $fillable = ['user_id', 'name', 'token', 'status'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function search($data)
    {
        $q = app('db')->table('Device');

        $q->select(
            'Device.id',
            'Device.name AS device_name',
            'Device.user_id',
            'Device.token',
            'Device.status',
            'Device.added_on',
            'Device.updated_on',
            'User.city_id',
            'User.name',
            'User.phone',
            'User.email',
            'User.mad_email'
        );
        $q->join("User", "User.id", '=', 'Device.user_id');
        
        if (!isset($data['status'])) {
            $data['status'] = '1';
        }
        if ($data['status'] !== false) {  // Setting status as '0' gets you even the disabled ones.
            $q->where('Device.status', $data['status']);
        }

        if (!empty($data['user_id'])) {
            $q->where('Device.user_id', $data['user_id']);
        }
        if (!empty($data['name'])) {
            $q->where('Device.name', $data['name']);
        }
        if (!empty($data['token'])) {
            $q->where('Device.token', $data['token']);
        }
        if (!empty($data['phone'])) {
            $q->where('User.phone', $data['phone']);
        }

        $q->orderBy('Device.added_on', 'desc');

        // dd($q->toSql(), $q->getBindings());
        $devices = $q->get();
        
        return $devices;
    }

    public function addOrActivate($data)
    {
        $devices = $this->search(['user_id' => $data['user_id'], 'token' => $data['token'], 'status' => false]); // status=false will return both active and inactive tokens.

        // Token not found. Create new device.
        if (!count($devices)) {
            $device = Device::create([
                'user_id'   => $data['user_id'],
                'name'      => isset($data['name']) ? $data['name'] : '',
                'token'     => $data['token'],
                'status'    => '1',
            ]);
        } else { // There are existing devices with given user_id/token combo
            // Then just activate those.
            foreach ($devices as $d) {
                $device = $this->find($d->id);
                $device->status = "1";
                $device->save();
            }
        }

        return $device;
    }
}
