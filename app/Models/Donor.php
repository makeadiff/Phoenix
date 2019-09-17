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
    protected $fillable = ['name','email', 'phone', 'added_on', 'address', 'donor_finance_id', 'added_by_user_id', 'updated_on'];

    public function donation()
    {
        return $this->hasMany('App\Models\Donation', 'donor_id');
    }

    /// Used to find a donor who matches multiple fields - email and phone. If none found, create the donor.
    public function findMatching($data, $added_by_user_id = 0)
    {
        // Find the donor - both email and phone must be same
        $donor = app('db')->table($this->table)->select('id')->where('email', $data['donor_email'])->where('phone', $data['donor_phone'])->first();

        // If we can't find the donor, add a new one.
        if (!$donor) {
            $donor_id = Donor::insertGetId([
                'name'  => $data['donor_name'],
                'email' => $data['donor_email'],
                'phone' => $data['donor_phone'],
                'address' => $data['donor_address'],
                'added_by_user_id' => $added_by_user_id,
                'added_on'=> date('Y-m-d H:i:s')
            ]);
            
            if ($donor_id) {
                return $donor_id;
            } else {
                return false;
            }
        }

        return $donor->id;
    }

    public function fetch($donor_id)
    {
        $this->id = $donor_id;
        $this->item = $this->find($donor_id);

        return $this->item;
    }
}
