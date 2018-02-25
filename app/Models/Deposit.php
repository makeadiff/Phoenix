<?php
namespace App\Models;

use App\Models\Common;
use App\Models\Donation;
use App\Models\User;

final class Deposit extends Common
{
    const CREATED_AT = 'added_on';
    const UPDATED_AT = 'reviewed_on';
    protected $table = 'Donut_Deposit';
    public $timestamps = true;
    protected $fillable = ['collected_from_user_id', 'given_to_user_id', 'reviewed_on', 'amount', 'status'];
    protected $national_account_user_id = 13257; // Pooja's User ID in Donut

    public function donations()
    {
        $donations = $this->belongsToMany('App\Models\Donation', 'Donut_DonationDeposit');
        return $donations->get();
    }

    public function collected_from() {
        $user = $this->belongsTo("App\Models\User", 'collected_from_user_id');
        return $user->first();
    }

    public function given_to() {
        $user = $this->belongsTo("App\Models\User", 'given_to_user_id');
        return $user->first();
    }

    public function add($collected_from_user_id, $given_to_user_id, $donation_ids) {
        // Validations...
        $user = new User;
        if(!$user->fetch($collected_from_user_id)) return $this->error("Invalid User ID of depositer.");
        if(!$user->fetch($given_to_user_id)) return $this->error("Invalid User ID of collector.");
        if($collected_from_user_id == $given_to_user_id) return $this->error("Depositer and collector can't be the same person.");

        // Check if any of the given donation has been part of an approved or pending deposit. Rejected deposits are ok.
        $donation = new Donation;
        foreach ($donation_ids as $donation_id) {
            $existing_donation = $donation->fetch($donation_id);
            if(!$existing_donation) return $this->error("Dontation $donation_id does not exist.");

            $pre_existing_deposit = false;
            foreach($existing_donation->deposit as $dep) {
                if(($dep->status == 'pending' or $dep->status == 'approved') and $dep->collected_from_user_id = $collected_from_user_id) {
                    $pre_existing_deposit = true; 
                    break;
                }
            }

            if($pre_existing_deposit) return $this->error("Dontation $donation_id is already deposited. You cannot deposit it again.");

            // :TODO: Check if this user has the ability to deposit this donation - must be a donation the user fundraised or approved at some point.
        }

        $amount = app('db')->table("Donut_Donation")->whereIn('id', $donation_ids)->sum('amount');

        // This should be Deposit::create - for for some reason its return can't be accessed - some private/protected issue, I think.
        $deposit_id = Deposit::insertGetId([
            'collected_from_user_id'=> $collected_from_user_id,
            'given_to_user_id'      => $given_to_user_id,
            'reviewed_on'           => '0000-00-00 00:00:00',
            'status'                => 'pending',
            'amount'                => $amount,
        ]);

        foreach ($donation_ids as $donation_id) {
            $donation = new Donation;
            $donation->find($donation_id)->edit([
                'status'        => 'deposited',
                'with_user_id'  => $given_to_user_id,
            ]);

            app('db')->table("Donut_DonationDeposit")->insert([
                'donation_id'   => $donation_id,
                'deposit_id'    => $deposit_id
            ]);
        }

        return $this->find($deposit_id);
    }

    // function search($params) {
    //     $this->sql_checks = array();
    //     $this->sql_joins = array();

    //     if(isset($params['reviewer_id'])) {
    //         $this->sql_checks['reviewer_id'] = "DP.given_to_user_id={$params['reviewer_id']}";
    //         $this->sql_checks['status'] = "DP.status='pending'";
    //     }
    //     if(isset($params['status'])) $this->sql_checks['status'] = "DP.status={$params['status']}";
    //     if(isset($params['status_in'])) $this->sql_checks['status'] = "DP.status IN (" . implode(",", $params['status_in']) . ")";

    //     // Only get donations after a preset date
    //     include('../../donutleaderboard/_city_filter.php');
    //     $from_date = $city_date_filter['25']['from']; // National start date
    //     $this->sql_checks['from_date'] = "DP.added_on >= '$from_date 00:00:00'";

    //     $deposits = $this->sql->getById("SELECT DP.id, DP.amount,DP.added_on, DP.reviewed_on, DP.status,
    //             DP.collected_from_user_id, TRIM(CONCAT(CFU.first_name, ' ', CFU.last_name)) AS collected_from_user_name,
    //             DP.given_to_user_id, TRIM(CONCAT(GTU.first_name,' ', GTU.last_name)) AS given_to_user_name
    //         FROM deposits DP
    //         INNER JOIN users GTU ON DP.given_to_user_id=GTU.id
    //         INNER JOIN users CFU ON DP.collected_from_user_id=CFU.id
    //         " . implode("\n", $this->sql_joins) . "
    //         WHERE " . implode(' AND ', $this->sql_checks) . "
    //         ORDER BY DP.added_on DESC");

    //     foreach($deposits as $id => $dep) {
    //         $deposits[$id]['donations'] = $this->getDonationsIn($id);
    //     }

    //     return $deposits;
    // }

    public function approve($current_user_id, $deposit_id = false) {
        $this->chain($deposit_id);

        $donations = $this->item->donations();
        foreach ($donations as $donation) {
            $donation->edit([
                'status'        => 'collected',
                'with_user_id'  => $current_user_id,
            ]);
        }

        return $this->changeStatus('approved', $current_user_id);
    }

    public function reject($current_user_id, $deposit_id = false) {
        $this->chain($deposit_id);

        return $this->changeStatus('rejected', $current_user_id);
    }

    public function changeStatus($status, $current_user_id, $deposit_id = false) {
        $this->chain($deposit_id);

        if(!$this->item) return false;
        if($this->item->given_to_user_id != $current_user_id) return $this->error("Current user don't have permission to approve/reject the deposit.");

        $this->item->status = $status;
        return $this->item->save();
    }

    public function fetch($deposit_id)
    {
        $deposit = $this->find($deposit_id);
        if(!$deposit) return false;

        $deposit->donations = $deposit->donations();

        return $deposit;
    }
}
