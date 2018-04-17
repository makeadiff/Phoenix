<?php
namespace App\Models;

use App\Models\Common;
use App\Models\Donation;

final class Donor extends Common
{
    const CREATED_AT = 'added_on';
    const UPDATED_AT = 'updated_on';
    protected $table = 'Donut_Donor';
    public $timestamps = true;
    protected $fillable = ['name','email', 'phone', 'added_on', 'address', 'added_by_user_id', 'updated_on'];

    public function donation()
    {
        $donations = $this->hasMany('App\Models\Donation', 'donor_id');
        return $donations->get();
    }

    /// Used to find a donor who matches multiple fields - email and phone. If none found, create the user.
    public function findMatching($data, $added_by_user_id = 0) {
        // Find the donor - both email and phone must be same
        $donor = app('db')->table($this->table)->select('id')->where('email', $data['donor_email'])->where('phone', $data['donor_phone'])->first();

        // If we can't find the donor, add a new one.
        if(!$donor) {
            $donor = Donor::create([
                'name'  => $data['donor_name'],
                'email' => $data['donor_email'],
                'phone' => $data['donor_phone'],
                'address' => $data['donor_address'],
                'added_by_user_id' => $added_by_user_id
            ]);
            if($donor) return $donor->id;
            else return false;
        } 

        return $donor->id;
    }

}
