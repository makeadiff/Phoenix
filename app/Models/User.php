<?php
namespace App\Models;

use App\Models\Group;
use App\Models\Log;
use App\Models\Common;
use App\Models\Classes;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

final class User extends Common
{
	const CREATED_AT = 'added_on';
	const UPDATED_AT = 'updated_on';
	protected $table = 'User';
	public $timestamps = true;
	protected $fillable = ['email','mad_email','phone','name','sex','password','password_hash','address','bio','source','birthday','city_id','credit','applied_role','status','user_type', 'joined_on', 'added_on', 'left_on'];
	public $enable_logging = true; // Used to disable logging the basic auth authentications for API Calls

	public function groups()
	{
		$groups = $this->belongsToMany('App\Models\Group', 'UserGroup', 'user_id', 'group_id')->where('Group.status', '=', '1')->wherePivot('year',$this->year)->select('Group.id','Group.vertical_id', 'Group.name', 'Group.type');
		$groups->orderByRaw("FIELD(Group.type, 'executive', 'national', 'strat', 'fellow', 'volunteer')");
		return $groups;
	}

	public function city()
	{
		return $this->belongsTo(City::class);
	}

	public function classes($status = '')
	{
		$classes = $this->belongsToMany("App\Models\Classes", 'UserClass', 'user_id', 'class_id')->where('Class.class_on', '>=', $this->year_start_time);
		if($status) $classes->where('Class.status', $status);
		$classes->orderBy("Class.class_on");
		return $classes;
	}

	/// Connects to all the batches the current user mentors.
	public function batches($status = false)
	{
		$batches = $this->hasMany("App\Models\Batch", 'batch_head_id');
		$batches->select("Batch.id", "Batch.class_time", "Batch.day", "Batch.batch_head_id");
		$batches->join("Class", "Class.batch_id", '=', 'Batch.id');
		$batches->where('Batch.year', '=', $this->year)->where("Class.class_on", '>', date('Y-m-d H:i:s'))->where("Batch.status", '=', '1');
		if($status) $batches->where('Class.status', $status);
		$batches->orderBy("Class.class_on");
		return $batches;
	}

	public function donations()
	{
		$donations = $this->hasMany("App\Models\Donation", 'fundraiser_user_id');
		$donations->where("added_on", '>=', $this->year_start_date);
		$donations->orderBy("added_on", 'desc');
		return $donations;
	}

	// public function data()
	// {
	// 	return $this->morphMany(Data::class, 'item', 'item_id');
	// }

	public function search($data) {
        $q = app('db')->table('User');
        $results = $this->baseSearch($data, $q)->get();

        // Add groups to each volunter that was returned.
		for($i=0; $i<count($results); $i++) {
			$results[$i]->groups = [];
			if(!isset($data['user_type']) or $data['user_type'] == 'volunteer') {
				$this_user = User::fetch($results[$i]->id);
				if($this_user->groups) {
					$results[$i]->groups = $this_user->groups;
				}
			}
		}

		return $results;
    }

	public function baseSearch($data, $q = false)
	{
		if(!$q) $q = app('db')->table($this->table);

		$q->select("User.id","User.name","User.email","User.phone","User.mad_email","User.credit","User.joined_on","User.left_on",
					"User.user_type","User.address","User.sex", "User.status", "User.city_id", app('db')->raw("City.name AS city_name"));
		$q->join("City", "City.id", '=', 'User.city_id');

		// Aliases. 
		if(isset($data['group_id']) and !isset($data['user_group'])) $data['user_group'] = $data['group_id'];
		if(isset($data['group_type']) and !isset($data['user_group_type'])) $data['user_group_type'] = $data['group_type'];

		if(!isset($data['status'])) $data['status'] = '1';
		if($data['status'] !== false) $q->where('User.status', $data['status']); // Setting status as '0' gets you even the deleted users

		if(isset($data['city_id']) and $data['city_id'] != 0) $q->where('User.city_id', $data['city_id']);

		if(empty($data['user_type'])) $data['user_type'] = 'volunteer';
		if(!empty($data['not_user_type'])) {
			$q->whereNotIn('User.user_type', $data['not_user_type']);
		} else if($data['user_type'] !== false) {
			$q->where('User.user_type', $data['user_type']);
		}

		if(!empty($data['id'])) $q->where('User.id', $data['id']);
		if(!empty($data['user_id'])) $q->where('User.id', $data['user_id']);
		if(!empty($data['name'])) $q->where('User.name', 'like', '%' . $data['name'] . '%');
		if(!empty($data['phone'])) $q->where('User.phone', $data['phone']);

		if(!empty($data['email'])) $q->where('User.email', $data['email']);
		if(!empty($data['mad_email'])) $q->where('User.mad_email', $data['mad_email']);
		if(!empty($data['any_email'])) $q->where('User.email', $data['any_email'])->orWhere("User.mad_email", $data['any_email']);

		if(!empty($data['identifier'])) {
			$q->where(function($query) use ($data) {
				$query->where('User.email', $data['identifier'])
					->orWhere("User.mad_email", $data['identifier'])
					->orWhere("User.phone", $data['identifier'])
					->orWhere("User.id", $data['identifier']);
			});
		}

		if(!empty($data['left_on'])) $q->where('DATE_FORMAT(User.left_on, "%Y-%m")', date('Y-m', strtotime($data['left_on'])));

		if(!empty($data['user_group'])) {
			if(!is_array($data['user_group'])) $data['user_group'] = array($data['user_group']);
			$q->join('UserGroup', 'User.id', '=', 'UserGroup.user_id');
			$q->whereIn('UserGroup.group_id', $data['user_group']);
			$q->where('UserGroup.year', $this->year);
			$q->distinct();
		}
		if(!empty($data['user_group_type'])) {
			$q->join('UserGroup', 'User.id', '=', 'UserGroup.user_id');
			$q->join('Group', 'Group.id', '=', 'UserGroup.group_id');
			$q->where('Group.type', $data['user_group_type']);
			$q->where('UserGroup.year', $this->year);
			$q->distinct();
		}
		if(!empty($data['vertical_id'])) {
			$q->join('UserGroup', 'User.id', '=', 'UserGroup.user_id');
			$q->join('Group', 'Group.id', '=', 'UserGroup.group_id');
			$q->where('Group.vertical_id', $data['vertical_id']);
			$q->where('UserGroup.year', $this->year);
			$q->distinct();
		}
		if(!empty($data['center_id'])) {
			$mentor_group_id = 8; // :HARDCODE:

			if(isset($data['user_group']) and in_array($mentor_group_id, $data['user_group'])) { // Find the mentors
				$q->join("Batch", 'User.id', '=', 'Batch.batch_head_id');
				$q->where('Batch.center_id', $data['center_id']);
				$q->where('Batch.year', $this->year);
				if(isset($data['project_id'])) $q->where('Batch.project_id', $data['project_id']);

			} else { // Find the teachers
				$q->join('UserClass', 'User.id', '=', 'UserClass.user_id');
				$q->join('Class', 'Class.id', '=', 'UserClass.class_id');
				$q->join('Level', 'Class.level_id', '=', 'Level.id');
				$q->where('Level.center_id', $data['center_id']);
				if(isset($data['project_id'])) $q->where('Level.project_id', $data['project_id']);
			}
			$q->distinct();
		}

		if(!empty($data['batch_id'])) {
			$q->join('UserBatch', 'User.id', '=', 'UserBatch.user_id');
			$q->addSelect("UserBatch.level_id");
			// $q->join('Batch', 'UserBatch.batch_id', '=', 'Batch.id');
			$q->where('UserBatch.batch_id', $data['batch_id']);
		}

		// Sorting
		if(!empty($data['user_type'])) {
			if($data['user_type'] == 'applicant') {
				$q->orderBy('User.joined_on','desc');
			} elseif($data['user_type'] == 'let_go') {
				$q->orderBy('User.left_on','desc');
			}
		}
		$q->orderby('User.name');

		// :TODO: Pagination
		// dd($q->toSql(), $q->getBindings(), $data);

		return $q;
	}

	public function inCity($city_id) {
		return $this->search(['city_id' => $city_id]);
	}

	public function fetch($user_id, $only_volunteers = true) {
		if(!$user_id) return false;

		$user = User::select('id', 'name', 'email', 'mad_email','phone', 'sex', 'photo', 'joined_on', 'address', 'birthday', 'left_on',
								'reason_for_leaving', 'user_type', 'status', 'credit', 'city_id')->where('status','1');
		if($only_volunteers) $user = $user->where('user_type', 'volunteer');

		$data = $user->find($user_id);
		if(!$data) return false;

		$data->groups = $data->groups()->get();
		$data->city = $data->city()->first()->name;

		return $data;
	}

	public function add($data)
	{
	    $q = app('db')->table($this->table);
	    $q->select('id','email','phone','user_type');
	    $q->where('user_type','<>','volunteer')->where('user_type','<>','applicant');
	    $q->where('email',$data['email'])->orWhere('phone',$data['phone']);

	    if(isset($data['mad_email'])){
	      $q->where('mad_email',$data['mad_email']);
	    }

	    $results = $q->first();
	    $zoho_user_id = isset($data['zoho_user_id']) ? $data['zoho_user_id'] : 0;

	    if(empty($results)){
	    	$user = User::create([
	          'email'     => $data['email'],
	          'mad_email' => isset($data['mad_email']) ? $data['mad_email'] : '',
	          'phone'     => User::correctPhoneNumber($data['phone']),
	          'name'      => $data['name'],
	          'sex'       => isset($data['sex']) ? $data['sex'] : 'f',
	          'password_hash' => Hash::make($data['password']),
	          'address'   => isset($data['address']) ? $data['address'] : '',
	          'bio'       => isset($data['bio']) ? $data['bio'] : '',
	          'source'    => isset($data['source']) ? $data['source'] : 'other',
	          'birthday'  => isset($data['birthday']) ? $data['birthday'] : null,
	          'city_id'   => $data['city_id'],
	          'applied_role'=>isset($data['profile']) ? $data['profile'] : '',
	          'credit'    => isset($data['credit']) ? $data['credit'] : '3',
	          'status'    => isset($data['status']) ? $data['status'] : '1',
	          'user_type' => isset($data['user_type']) ? $data['user_type'] : 'applicant',
	          'joined_on' => isset($data['joined_on']) ? $data['joined_on'] : date('Y-m-d H:i:s'),
	          'zoho_user_id'=>$zoho_user_id
	      	]);

	    } else {
			$user = User::where('id',$results->id)->first();
			$user->email        = $data['email'];
			$user->mad_email    = isset($data['mad_email']) ? $data['mad_email'] : '';
			$user->phone        = User::correctPhoneNumber($data['phone']);
			$user->name         = $data['name'];
			$user->sex          = isset($data['sex']) ? $data['sex'] : 'f';
			$user->password_hash= Hash::make($data['password']);
			$user->address      = isset($data['address']) ? $data['address'] : '';
			$user->bio          = isset($data['bio']) ? $data['bio'] : '';
			$user->source       = isset($data['source']) ? $data['source'] : 'other';
			$user->birthday     = isset($data['birthday']) ? $data['birthday'] : null;
			$user->city_id      = $data['city_id'];
			$user->applied_role = isset($data['profile']) ? $data['profile'] : '';
			$user->credit       = isset($data['credit']) ? $data['credit'] : '3';
			$user->status       = isset($data['status']) ? $data['status'] : '1';
			$user->user_type    = isset($data['user_type']) ? $data['user_type'] : 'applicant';
			$user->joined_on    = isset($data['joined_on']) ? $data['joined_on'] : date('Y-m-d H:i:s');
			$user->save();
	    }
		
		if($user and !$zoho_user_id) {
			// Send Data to Zoho
			$all_sexes = [
				'm'     => 'Male',
				'f'     => 'Female',
				'o'     => 'Other'
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
				25 => 'Dehradun',
				26 => 'Leadership',
				28 => 'Test'
			];
			$role_types = [
				'teaching'	=> 'Teaching Volunteer',
				'wingman'	=> 'Wingman (Youth Mentoring)',
				'fundraising'=>'Fundraising Volunteer',
				'hc'		=> 'Human Capital Volunteer',
				'other'		=> 'Other'
			];
			$client = new Client(['http_errors' => false]); //GuzzleHttp\Client
			$response = '';
			try {
				$result = $client->post('https://creator.zoho.com/api/jithincn1/json/recruitment-management/form/Registration/record/add', [
					'form_params' => [
						'authtoken'         => '4f1c9be22a7d8fdd2c2a1a6e2921fa13',
						'scope'             => 'creatorapi',
						'campaign_id'       => isset($data['campaign']) ? $data['campaign'] : '',
						'Applicant_Name'    => $data['name'],
						'Gender'            => isset($data['sex']) ? $all_sexes[$data['sex']] : 'Female',
						'City'              => $all_cities[$data['city_id']],
						'Date_of_Birth'     => isset($data['birthday']) ? date('d-M-Y', strtotime($data['birthday'])) : '01-Jan-2000',
						'Email'             => $data['email'],
						'Address_for_correspondence'    => isset($data['address']) ? $data['address'] : '',
						'Mobile_Number'     => $data['phone'],
						'Occupation'        => isset($data['job_status']) ? ucwords($data['job_status']) : '',
						'Role_Type'			=> $role_types[$data['profile']],
						'Reason_for_choosing_to_volunteer_at_MAD'   => isset($data['why_mad']) ? $data['why_mad'] : '',
						'MAD_Applicant_Id'  => $user->id,  // 'Unique_Applicant_ID'    => $status['id'],
					]
				]);
				$response = $result->getBody();
			} catch(Exception $e) {
				// Can't send data to Zoho

			} finally {
				if($response) {
					$zoho_response = json_decode($response);
					$zoho_user_id = @$zoho_response->formname[1]->operation[1]->values->ID;

					if($zoho_user_id and $user->id) {
						$user->zoho_user_id = $zoho_user_id;
						$user->save();
					}
				}
			}
			// $user->zoho_response = $zoho_response; // Use this if you want to debug the zoho call. This will show up in the AJAX call to our API
		}

		return $user;
	}


	public function edit($data, $user_id = false)
	{
		$this->chain($user_id);

		foreach ($this->fillable as $key) {
			if(!isset($data[$key])) continue;

			if($key == 'phone') $data[$key] = $this->correctPhoneNumber($data[$key]);
			if($key == 'password') $data['password_hash'] = Hash::make($data[$key]);

			$this->item->$key = $data[$key];
		}
		$this->item->save();

		return $this->item;
	}

	public function addGroup($group_id, $user_id = false)
	{
		$this->chain($user_id);

		// Check if the user has the group already.
		$existing_groups = $this->groups()->get();
		$group_found = false;
		foreach ($existing_groups as $grp) {
			if($grp->id == $group_id) {
				$group_found = true;
				break;
			}
		}

		if($group_found) return false;

		app('db')->table("UserGroup")->insert([
			'user_id'   => $this->id,
			'group_id'  => $group_id,
			'year'      => $this->year
		]);

		return $this->groups();
	}

	public function removeGroup($group_id, $user_id = false)
	{
		$this->chain($user_id);

		// Check if the user has the group.
		$existing_groups = $this->groups()->get();
		$group_found = false;
		foreach ($existing_groups as $grp) {
			if($grp->id == $group_id) {
				$group_found = true;
				break;
			}
		}

		if(!$group_found) return false;

		app('db')->table("UserGroup")->where([
			'user_id'   => $this->id,
			'group_id'  => $group_id,
			'year'      => $this->year
		])->delete();

		return $this->groups();
	}

	public function setCredit($credit, $user_id = false)
	{
		$this->chain($user_id);

		$this->item->credit = $credit;
		return $this->item->save();
	}

	public function editCredit($new_credit, $credit_assigned_by_user_id, $reason, $user_id = false)
	{
		$this->chain($user_id);

		app('db')->table('UserCredit')->insert([
			'user_id'   => $this->id,
			'credit'    => $new_credit,
			'credit_assigned_by_user_id' => $credit_assigned_by_user_id,
			'comment'   => $reason,
			'added_on'  => date('Y-m-d H:i:s'),
			'year'      => $this->year
		]);

		return $this->find($this->id)->setCredit($new_credit);
	}

	public function login($email_or_phone, $password, $auth_token='')
	{
		$user = User::where('status', '1')->where('user_type','volunteer');
		$user->where(function($q) use ($email_or_phone) {
			$q->where('email', $email_or_phone)->orWhere('phone', $email_or_phone)->orWhere('mad_email', $email_or_phone);
		});
		$data = $user->first();

		if($data) {
			$is_correct = false;
			if($password) {
				$is_correct = Hash::check($password, $data->password_hash);
			} elseif($auth_token) {
				$is_correct = ($data->auth_token == $auth_token);
			}

			if(!$is_correct) { // Incorrect password / Auth key
				$data = null;
				$this->errors[] = "Incorrect password provided";
			} else {
				$user_id = $data->getKey();

				if($this->enable_logging) {
					// Log the login
					$login_type = 'Username/Password';
					if($auth_token) $login_type = 'Auth Token';
					Log::add(['name' => 'user_login', 'user_id' => $user_id, 'data' => ['entry_point' => 'Phoenix', 'login_type' => $login_type]]);
				}

				return $this->fetch($user_id);
			}

		} else {
			$this->errors[] = "Can't find any user with the given email/phone";
		}
		return false;
	}

	/// Changes the phone number format from +91976063565 to 9746063565. Remove the 91 at the starting.
	private function correctPhoneNumber($phone) {
		if(strlen($phone) > 10) {
			return preg_replace('/^\+?91\D?/', '', $phone);
		}
		return $phone;
	}
}
