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
    protected $national_account_user_id = 163416; // National Finance User ID.

    public function donations()
    {
        $connection = $this->belongsToMany('App\Models\Donation', 'Donut_DonationDeposit');

        // $donations = $connection->get();
        // foreach ($donations as $i => $don) {
        //     $donations[$i]->donor = $don->donor()->first()->name;
        //     $donations[$i]->fundraiser = $don->fundraiser()->first()->name;
        // }
        return $connection;
    }

    public function collected_from()
    {
        return $this->belongsTo("App\Models\User", 'collected_from_user_id');
    }

    public function given_to()
    {
        return $this->belongsTo("App\Models\User", 'given_to_user_id');
    }

    public function add($collected_from_user_id, $given_to_user_id, $donation_ids, $deposit_information = '')
    {
        // Validations...
        $user = new User;
        if (!$user->fetch($collected_from_user_id)) {
            return $this->error("Invalid User ID of depositer.");
        }
        if (!$user->fetch($given_to_user_id)) {
            return $this->error("Invalid User ID of collector.");
        }
        if ($collected_from_user_id == $given_to_user_id) {
            return $this->error("Depositer and collector can't be the same person.");
        }
        if (!$donation_ids or !count($donation_ids)) {
            return $this->error("No donations selected to be part of the deposit.");
        }

        // Check if any of the given donation has been part of an approved or pending deposit. Rejected deposits are ok.
        $donation = new Donation;
        foreach ($donation_ids as $donation_id) {
            $existing_donation = $donation->fetch($donation_id, true);
            if (!$existing_donation) {
                return $this->error("Dontation $donation_id does not exist.");
            }

            // See if the donation has been deposited already.
            $pre_existing_deposit = false;
            if (count($existing_donation->deposit)) {
                foreach ($existing_donation->deposit as $dep) {
                    if (($dep->status == 'pending' or $dep->status == 'approved')
                            and $dep->collected_from_user_id == $collected_from_user_id) {
                        $pre_existing_deposit = true;
                        break;
                    }
                }
            }

            if ($pre_existing_deposit) {
                return $this->error("Dontation $donation_id is already deposited. You cannot deposit it again.");
            }

            // :TODO: Check if this user has the ability to deposit this donation - must be a donation the user fundraised or approved at some point.
            //          Are both collected_from_user_id and given_to_user_id in the same city - except for the Finance fellow -> National deposit.
        }

        $amount = app('db')->table("Donut_Donation")->whereIn('id', $donation_ids)->sum('amount');

        // This should be Deposit::create - for for some reason its return can't be accessed - some private/protected issue, I think.
        $deposit_id = Deposit::insertGetId([
            'collected_from_user_id'=> $collected_from_user_id,
            'given_to_user_id'      => $given_to_user_id,
            'reviewed_on'           => null,
            'added_on'              => date('Y-m-d H:i:s'),
            'status'                => 'pending',
            'amount'                => $amount,
            'deposit_information'   => $deposit_information
        ]);

        foreach ($donation_ids as $donation_id) {
            $donation = new Donation;
            $donation->find($donation_id)->edit([
                'status'        => 'deposited',
                //'with_user_id'  => $given_to_user_id, // This gets updated only after approved.
            ]);

            app('db')->table("Donut_DonationDeposit")->insert([
                'donation_id'   => $donation_id,
                'deposit_id'    => $deposit_id
            ]);
        }

        return $this->find($deposit_id);
    }

    public function search($data)
    {
        $q = app('db')->table($this->table);

        $q->select(
            "Donut_Deposit.id",
            "Donut_Deposit.amount",
            "Donut_Deposit.added_on",
            "Donut_Deposit.reviewed_on",
            "Donut_Deposit.status",
            "Donut_Deposit.collected_from_user_id",
            "Donut_Deposit.given_to_user_id",
            "Donut_Deposit.deposit_information"
        );

        if (!empty($data['reviewer_user_id'])) {
            $q->where('Donut_Deposit.given_to_user_id', $data['reviewer_user_id']);
            $q->where('Donut_Deposit.status', 'pending');
        }

        if (!empty($data['id'])) {
            $q->where('Donut_Deposit.id', $data['id']);
        }
        if (!empty($data['status'])) {
            $q->where('Donut_Deposit.status', $data['status']);
        }
        if (!empty($data['status_in'])) {
            $q->whereIn('Donut_Deposit.status', $data['status_in']);
        }

        $donation = new Donation;
        $q->where('Donut_Deposit.added_on', '>', $donation->start_date);

        $q->orderBy('Donut_Deposit.added_on', 'desc');
        $deposits = $q->get();
        // dd($q->toSql(), $q->getBindings());

        $user = new User;

        foreach ($deposits as $index => $dep) {
            $deposits[$index]->collected_from_user_name = $user->find($dep->collected_from_user_id)->name;
            $deposits[$index]->donations = $this->find($dep->id)->donations()->get();
        }

        return $deposits;
    }

    public function approve($current_user_id, $deposit_id = false)
    {
        $this->chain($deposit_id);

        if (!$current_user_id) {
            return $this->error("Please include the ID of the user reviewing the deposit.");
        }

        $donations = $this->item->donations()->get();
        if (!count($donations)) {
            return $this->error("There are no donations associated with this deposit.");
        }

        foreach ($donations as $donation) {
            $donation->approve($current_user_id, $this->item->given_to_user_id, 'queue');
        }

        return $this->changeStatus('approved', $current_user_id);
    }

    public function reject($current_user_id, $deposit_id = false)
    {
        $this->chain($deposit_id);

        return $this->changeStatus('rejected', $current_user_id);
    }

    public function changeStatus($status, $current_user_id, $deposit_id = false)
    {
        $this->chain($deposit_id);

        if (!$this->item) {
            return false;
        }
        if (($this->item->given_to_user_id != $current_user_id) // The person who the deposit was given to should be the one approving it.
                and ($this->item->given_to_user_id != $this->national_account_user_id)) { // But if it is given to the national account, then it can be approved my mupltiple people
            return $this->error("Current user don't have permission to approve/reject the deposit.");
        }

        $this->item->status = $status;
        $this->item->save();

        return $this->item;
    }

    public function fetch($deposit_id)
    {
        $deposit = $this->find($deposit_id);
        if (!$deposit) {
            return false;
        }

        $deposit->donations = $deposit->donations()->get();

        return $deposit;
    }
}
