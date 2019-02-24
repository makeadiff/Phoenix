<?php
namespace App\Models;

use App\Models\Common;

final class Contact extends Common  
{
    protected $table = 'Contact';
    const CREATED_AT = 'added_on';
    const UPDATED_AT = 'updated_on';
    protected $fillable = ['email','phone','name','sex','address','source','birthday','city_id','company','latitude','longitute','job_status', 'status', 'why_mad'];
    
    public static function search($data)
    {
        $search_fields = ['id', 'name','city_id', 'email', 'phone', 'is_applicant', 'is_subscribed', 'is_care_collective'];
        $q = app('db')->table('Contact');
        $q->select('id', 'name', 'email', 'phone', 'city_id', 'added_on', 'source');
        $q->where('status', '1');

        foreach ($search_fields as $field) {
            if(empty($data[$field])) continue;

            if($field === 'name') $q->where($field, 'like', '%' . $data[$field] . '%');
            else $q->where($field, $data[$field]);
        }
        $q->orderBy('city_id', 'name');
        $results = $q->get();

        return $results;
    }

    public static function getCount()
    {
        $care_collective_list = app('db')->table('Contact')->where('is_care_collective','1')->where('status','1');
        $care_collective_list_count = $care_collective_list->count();
        return $care_collective_list_count;
    }


    public function add($data)
    {
        $contact = Contact::create([
            'name' => $data['name'],
            'email'=> $data['email'],
            'phone'=> $data['phone'],
            'city_id' => $data['city_id'],
            'birthday' => isset($data['birthday']) ? $data['birthday'] : '',
            'sex' => isset($data['sex']) ? $data['sex'] : 'f',
            'source' => isset($data['source']) ? $data['source'] : 'other',
            'address' => isset($data['address']) ? $data['address'] : '',
            'why_mad' => isset($data['why_mad']) ? $data['why_mad'] : '',
            'job_status' => isset($data['job_status']) ? $data['job_status'] : 'other',
            'is_applicant' => isset($data['is_applicant']) ? $data['is_applicant'] : 0,
            'is_subscribed' => isset($data['is_subscribed']) ? $data['is_subscribed'] : 0,
            'is_care_collective' => isset($data['is_care_collective']) ? $data['is_care_collective'] : 0,
            'status'	=> 1
        ]);

        return $contact;
    }
}