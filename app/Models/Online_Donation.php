<?php
namespace App\Models;

use App\Models\Common;
use App\Models\User;
use App\Models\Donor;
use JSend;
use Illuminate\Database\Eloquent\Model;

final class Online_Donation extends Model
{
    use Common;
    
    protected $table = 'Online_Donation';
    public $timestamps = true;
    const CREATED_AT = 'added_on';
    const UPDATED_AT = null;
    public $start_date = '2019-05-01 00:00:00'; // Year will get re-written in the constructor.
    protected $fillable = ['donor_id','amount', 'payment', 'payment_method', 'currency', 'gateway', 'gateway_transaction_id', 'info', 'fundraiser_user_id', 
                            'repeat_count', 'unit_amount', 'conversion_rate', 'receipt_number', 'frequency'];

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->start_date = $this->year() . '-05-01 00:00:00';
    }
    
    public function fundraiser()
    {
        return $this->belongsTo('App\Models\User', 'fundraiser_user_id');
    }

    public function donor()
    {
        return $this->belongsTo('App\Models\Donor', 'donor_id');
    }

    public function search($data)
    {
        $q = app('db')->table('Online_Donation');
        $q->select(
            'Online_Donation.id',
            'Online_Donation.id AS online_donation_id',
            'Online_Donation.fundraiser_user_id',
            'Online_Donation.donor_id',
            'Online_Donation.amount',
            'Online_Donation.payment',
            'Online_Donation.info',
            'Online_Donation.added_on',
            'Online_Donation.frequency',
            'Online_Donation.receipt_number',
            'User.city_id',
            app('db')->raw('User.name AS fundraiser'),
            app('db')->raw('Donut_Donor.name AS donor')
        );
        $q->join("User", "User.id", '=', 'Online_Donation.fundraiser_user_id');
        $q->join("Donut_Donor", "Donut_Donor.id", '=', 'Online_Donation.donor_id');
        
        if (!empty($data['id'])) {
            $q->where('Online_Donation.id', $data['id']);
        }
        if (!empty($data['donation_id'])) {
            $q->where('Online_Donation.id', $data['donation_id']);
        }
        if (!empty($data['city_id'])) {
            $q->where('User.city_id', $data['city_id']);
        }
        if (!empty($data['amount'])) {
            $q->where('Online_Donation.amount', $data['amount']);
        }
        if (!empty($data['payment'])) {
            $q->where('Online_Donation.payment', $data['payment']);
        } else {
            $q->where('Online_Donation.payment', 'success'); // If payment is not specifically mentioned, its assumed to be 'success' - only successful donations will be returned.
        }
        if (!empty($data['not_payment'])) {
            $q->where('Online_Donation.payment', '!=', $data['not_payment']);
        }

        if (!empty($data['from'])) {
            $q->where('Online_Donation.added_on', '>', date('Y-m-d 00:00:00', strtotime($data['from'])));
        } elseif (empty($data['id'])) {
            $q->where('Online_Donation.added_on', '>', $this->start_date);
        } // If ID is given, should find donation anywhere in history - not just this year.
        if (!empty($data['to'])) {
            $q->where('Online_Donation.added_on', '<', date('Y-m-d 00:00:00', strtotime($data['to'])));
        }
        if (!empty($data['fundraiser_user_id'])) {
            $q->where('Online_Donation.fundraiser_user_id', $data['fundraiser_user_id']);
        }

        if (!empty($data['donor_id'])) {
            $q->where('Donut_Donor.id', $data['donor_id']);
        }
        if (!empty($data['donor_email'])) {
            $q->where('Donut_Donor.email', $data['donor_email']);
        }
        if (!empty($data['donor_phone'])) {
            $q->where('Donut_Donor.phone', $data['donor_phone']);
        }
        if (!empty($data['donor_name'])) {
            $q->where('Donut_Donor.name', 'LIKE', '%' . $data['donor_name'] . '%');
        }

        $q->orderBy('Online_Donation.added_on', 'desc');

        // dd($q->toSql(), $q->getBindings(), $data);

        return $q;
    }

    /// Get all the donations donuted by the given user
    public function byFundraiser($user_id)
    {
        return $this->search(['fundraiser_user_id' => $user_id]);
    }

    /// Get all the donations from the given donor
    public function byDonor($donor_id)
    {
        return $this->search(['donor_id' => $donor_id]);
    }

    public function fetch($donation_id)
    {
        $data = Online_Donation::search(['id' => $donation_id]);
        $data = $data[0];

        $this->id = $donation_id;
        $this->item = $this->find($donation_id);

        return $data;
    }

    // :TODO: public function add($data) {}

    public function edit($data, $donation_id = false)
    {
        $this->chain($donation_id);

        if (!$this->item) {
            return false;
        }
        
        foreach ($this->fillable as $key) {
            if (!isset($data[$key])) {
                continue;
            }

            $this->item->$key = $data[$key];
        }
        $this->item->save();

        return $this->item;
    }

}
