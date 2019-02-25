<?php
namespace App\Models;

use App\Models\Common;
use JSend;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

final class Contact extends Common  
{
    protected $table = 'Contact';
    const CREATED_AT = 'added_on';
    const UPDATED_AT = 'updated_on';
    protected $fillable = ['email','phone','name','sex','address','source','birthday','city_id','company','latitude','longitute','job_status', 'status', 'why_mad', 'is_applicant', 'is_subscribed', 'is_care_collective'];
    
    public static function search($data)
    {
        $search_fields = ['id', 'name','city_id', 'email', 'phone', 'is_applicant', 'is_subscribed', 'is_care_collective'];
        $q = app('db')->table('Contact');
        $q->select('id', 'name', 'email', 'phone', 'city_id', 'added_on', 'source', 'is_applicant', 'is_subscribed', 'is_care_collective' );
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
        $exists = $this->search(['email' => $data['email']]); // Check if it already exits
        if(count($exists)) {
            $contact = $exists[0];
            if(
                ($contact->is_care_collective != @$data['is_care_collective']) or
                ($contact->is_applicant != @$data['is_applicant']) or
                ($contact->is_subscribed != @$data['is_subscribed'])
            ) {
                $this->item_id = $contact->id;
                return $this->edit($data);
            } else {
                $this->errors = ["The given Email is already in our system."];
                return false;
            }
        }

        $validation_rules = [
            'name'      => 'max:50',
            'email'     => 'required|email|unique:Contact',
            'phone'     => 'unique:Contact|regex:/[\+0-9]{10,13}/',
            'city_id'   => 'numeric|exists:City,id'
        ];
        
        $validator = \Validator::make($data, $validation_rules);

        if ($validator->fails()) {
        	$this->errors =  $validator->errors();
            return false;
        }

        $contact = Contact::create([
            'name' => isset($data['name']) ? $data['name'] : '',
            'email'=> $data['email'],
            'phone'=> isset($data['phone']) ? $this->correctPhoneNumber($data['phone']) : '',
            'city_id' => isset($data['city_id']) ? $data['city_id'] : '0',
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

        if($contact and $contact->is_applicant)	{
			// Send Data to Zoho
			$all_sexes = [
				'm'		=> 'Male',
				'f'		=> 'Female',
				'o'		=> 'Other'
			];
			$all_cities = [
				0 => 'None',
				1 => 'Bangalore',
				2 => 'Mangalore',
				3 => 'Trivandrum',
				4 => 'Mumbai',
				5 => 'Pune',
				6 => 'Chennai',
				8 => 'Vellore',
				10 => 'Cochin',
				11 => 'Hyderabad',
				12 => 'Delhi',
				13 => 'Chandigarh',
				14 => 'Kolkata',
				15 => 'Nagpur',
				16 => 'Coimbatore',
				17 => 'Vizag',
				18 => 'Vijayawada',
				19 => 'Gwalior',
				20 => 'Lucknow',
				21 => 'Bhopal',
				22 => 'Mysore',
				23 => 'Guntur',
				24 => 'Ahmedabad',
				25 => 'Dehradun'];
			$client = new Client(); //GuzzleHttp\Client
			$result = $client->post('https://creator.zoho.com/api/jithincn1/json/recruitment-management/form/Registration/record/add', [
			    'form_params' => [
   					'authtoken'			=> '205aee93fdc5f6d2d61b5833625f86ce',
					'scope'				=> 'creatorapi',
					'campaign_id' 		=> isset($data['campaign']) ? $data['campaign'] : '',
					'Applicant_Name'	=> $data['name'],
					'Gender'			=> isset($data['sex']) ? $all_sexes[$data['sex']] : 'Female',
					'City'				=> $all_cities[$data['city_id']],
					'Date_of_Birth'		=> isset($data['birthday']) ? date('d-M-Y', strtotime($data['birthday'])) : '01-01-2000',
					'Email'				=> $data['email'],
					'Address_for_correspondence'	=> isset($data['address']) ? $data['address'] : '',
					'Mobile_Number'		=> $data['phone'],
					'Occupation'		=> isset($data['job_status']) ? $data['job_status'] : '',
					'Reason_for_choosing_to_volunteer_at_MAD'	=> isset($data['why_mad']) ? $data['why_mad'] : '',
					'MAD_Applicant_Id'	=> $contact['id'],	// 'Unique_Applicant_ID'	=> $status['id'],
			    ]
			]);
			$response = $result->getBody();
			$zoho_response = json_decode($response);
			$zoho_user_id = @$zoho_response->formname[1]->operation[1]->values->ID;

			if($zoho_user_id) $this->setInfo($contact->id, 'zoho_user_id', $zoho_user_id);
		}

        return $contact;
    }

    public function edit($data, $contact_id = false)
    {
        $this->chain($contact_id);

        foreach ($this->fillable as $key) {
            if(!isset($data[$key])) continue;

            if($key == 'phone') $data[$key] = $this->correctPhoneNumber($data[$key]);
    
            $this->item->$key = $data[$key];
        }
        $this->item->save();

        return $this->item;
    }

    public function setInfo($contact_id, $key, $value)
    {
    	$contact = Contact::find($contact_id);

    	$info = [];
    	if($contact->info) {
    		$info = json_decode($contact->info);
    	}

    	$info[$key] = $value;
    	$contact->info = json_encode($info);
    	return $contact->save();
    }

    /// Changes the phone number format from +91976063565 to 9746063565. Remove the 91 at the starting.
    private function correctPhoneNumber($phone) 
    {
        if(strlen($phone) > 10) {
            return preg_replace('/^\+?91\D?/', '', $phone);
        }
        return $phone;
    }
}