<?php
namespace App\Models;

use App\Models\Common;
use App\Models\User;
use App\Models\Donor;
use App\Libraries\SMS;
use App\Libraries\Email;
use Illuminate\Support\Facades\Storage;
use \Datetime;

final class Donation extends Common
{
    protected $table = 'Donut_Donation';
    public $timestamps = true;
    const CREATED_AT = 'added_on';
    const UPDATED_AT = 'updated_on';
    public $start_date = '2019-05-01 00:00:00'; // Year will get re-written in the constructor.
    protected $fillable = ['type', 'fundraiser_user_id', 'donor_id', 'with_user_id', 'status', 'amount', 'reference_file', 'cheque_no', 'added_on', 'updated_on',
                            'nach_start_on', 'nach_end_on', 'donation_repeat_count', 'updated_by_user_id', 'comment'];
    protected $donation_statuses = ['collected', 'deposited', 'receipted'];
    protected $national_account_user_id = 163416; // National Finance User ID.

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->start_date = $this->year . '-05-01 00:00:00';
    }
    
    public function fundraiser()
    {
        return $this->belongsTo('App\Models\User', 'fundraiser_user_id');
    }

    public function donor()
    {
        return $this->belongsTo('App\Models\Donor', 'donor_id');
    }

    public function deposit()
    {
        return $this->belongsToMany("App\Models\Deposit", 'Donut_DonationDeposit', 'donation_id', 'deposit_id')->where('Donut_Deposit.status', '!=', 'rejected');
    }

    public function search($data)
    {
        $q = app('db')->table('Donut_Donation');
        $donations = $this->baseSearch($data, $q)->get();

        // Find only deposited or undeposited donations - also used to include deposit info.
        if (isset($data['deposited']) or (isset($data['include_deposit_info']) and $data['include_deposit_info'])) {
            foreach ($donations as $index => $don) {
                $donation_id = $don->id;
                // Find all donations deposited by the current user which is still in pending or approved.
                $q = app('db')->table("Donut_Deposit AS DP");
                $q->select('DP.*', 'GU.name AS given_to_user_name', 'CU.name AS collected_from_user_name');
                $q->join("Donut_DonationDeposit AS DD", 'DD.deposit_id', '=', 'DP.id');
                $q->join("User AS GU", 'GU.id', '=', 'DP.given_to_user_id');
                $q->join("User AS CU", 'CU.id', '=', 'DP.collected_from_user_id');
                $q->where("DD.donation_id", $donation_id);
                $q->whereIn("DP.status", ['approved', 'pending']);
                $q->orderBy("DP.added_on", 'desc');

                if (!empty($data['approver_user_id'])) {
                    $q->where("DP.collected_from_user_id", $data['approver_user_id']);
                }

                $deposit_info = $q->get();

                if (isset($data['include_deposit_info']) and $data['include_deposit_info']) {
                    $donations[$index]->deposit = $deposit_info;
                }

                if (isset($data['deposited'])) {
                    $last_deposit = $q->first();

                    // Find donations which had are in the deposits table with status of pending or approved
                    if (!$deposit_info and $data['deposited']) {
                        unset($donations[$index]); // Deposit info not present - undeposited.
                    }
                    if ($last_deposit and isset($last_deposit->status) and ($last_deposit->status == 'approved' or $last_deposit->status == 'pending')) {// Approved or pending deposit
                        if (!$data['deposited']) { // Find un-deposited donuts - deposited=false
                            unset($donations[$index]);
                        }
                    } elseif ($data['deposited']) {  // Only deposited donations go thru.
                        unset($donations[$index]);
                    }
                }
            }
        }

        return $donations;
    }

    public function baseSearch($data, $q = false)
    {
        if (!$q) {
            $q = app('db')->table('Donut_Donation');
        }

        $q->select(
            "Donut_Donation.id",
            'Donut_Donation.type',
            'Donut_Donation.fundraiser_user_id',
            'Donut_Donation.donor_id',
            'Donut_Donor.donor_finance_id',
            'Donut_Donation.with_user_id',
            'Donut_Donation.status',
            'Donut_Donation.amount',
            'Donut_Donation.reference_file',
            'Donut_Donation.cheque_no',
            'Donut_Donation.added_on',
            'Donut_Donation.updated_on',
            'Donut_Donation.updated_by_user_id',
            'Donut_Donation.comment',
            'Donut_Donation.nach_start_on',
            'Donut_Donation.nach_end_on',
            'User.city_id',
            app('db')->raw('User.name AS fundraiser'),
            app('db')->raw('Donut_Donor.name AS donor')
        );
        $q->join("User", "User.id", '=', 'Donut_Donation.fundraiser_user_id');
        $q->join("Donut_Donor", "Donut_Donor.id", '=', 'Donut_Donation.donor_id');
        
        if (!empty($data['id'])) {
            $q->where('Donut_Donation.id', $data['id']);
        }
        if (!empty($data['donation_id'])) {
            $q->where('Donut_Donation.id', $data['donation_id']);
        }
        if (!empty($data['city_id'])) {
            $q->where('User.city_id', $data['city_id']);
        }
        if (!empty($data['amount'])) {
            $q->where('Donut_Donation.amount', $data['amount']);
        }
        if (!empty($data['status'])) {
            $q->where('Donut_Donation.status', $data['status']);
        }
        if (!empty($data['not_status'])) {
            $q->where('Donut_Donation.status', '!=', $data['not_status']);
        }
        if (!empty($data['type'])) {
            $q->where('Donut_Donation.type', $data['type']);
        }
        if (!empty($data['type_in'])) {
            $q->whereIn('Donut_Donation.type', $data['type_in']);
        }
        if (!empty($data['from'])) {
            $q->where('Donut_Donation.added_on', '>', date('Y-m-d 00:00:00', strtotime($data['from'])));
        } elseif (empty($data['id'])) {
            $q->where('Donut_Donation.added_on', '>', $this->start_date);
        } // If ID is given, should find donation anywhere in history - not just this year.
        if (!empty($data['to'])) {
            $q->where('Donut_Donation.added_on', '<', date('Y-m-d 00:00:00', strtotime($data['to'])));
        }
        if (!empty($data['fundraiser_user_id'])) {
            $q->where('Donut_Donation.fundraiser_user_id', $data['fundraiser_user_id']);
        }
        if (!empty($data['updated_by_user_id'])) {
            $q->where('Donut_Donation.updated_by_user_id', $data['updated_by_user_id']);
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

        if (isset($data['deposited']) or (isset($data['include_deposit_info']) and $data['include_deposit_info'])) { //If either of these are set get only cash/cheque donations - and NACH forms.
            $q->whereIn("Donut_Donation.type", ['cash', 'cheque', 'nach']);
        }

        if (!empty($data['approver_user_id'])) {
            $q->join("Donut_DonationDeposit", "Donut_DonationDeposit.donation_id", '=', 'Donut_Donation.id');
            $q->join("Donut_Deposit", "Donut_DonationDeposit.deposit_id", '=', 'Donut_Deposit.id');
            $q->where("Donut_Deposit.given_to_user_id", $data['approver_user_id']);
            if (!isset($data['deposit_status'])) {
                $q->where('Donut_Deposit.status', 'approved');
            }
        }
        if (!empty($data['deposit_status'])) {
            $q->join("Donut_DonationDeposit", "Donut_DonationDeposit.donation_id", '=', 'Donut_Donation.id');
            $q->join("Donut_Deposit", "Donut_DonationDeposit.deposit_id", '=', 'Donut_Deposit.id');
            if (!isset($data['deposit_status'])) {
                $q->where('Donut_Deposit.status', $data['deposit_status']);
            }
        }
        if (!empty($data['deposit_status_in'])) {
            $q->join("Donut_DonationDeposit", "Donut_DonationDeposit.donation_id", '=', 'Donut_Donation.id');
            $q->join("Donut_Deposit", "Donut_DonationDeposit.deposit_id", '=', 'Donut_Deposit.id');
            $q->where(function ($q) use ($data) {
                foreach ($data['deposit_status_in'] as $deposit_status) {
                    $q->orWhere('Donut_Deposit.status', $deposit_status);
                }
            });
        }
        $q->orderBy('Donut_Donation.added_on', 'desc');

        // dd($q->toSql(), $q->getBindings(), $data);

        return $q;
    }

    /// Get all the donations donuted by the given user
    public function byFundraiser($user_id)
    {
        return $this->search(array('fundraiser_user_id' => $user_id, 'include_deposit_info' => true));
    }

    /// Get all the donations from the given donor
    public function byDonor($donor_id)
    {
        return $this->search(array('donor_id' => $donor_id));
    }

    public function fetch($donation_id, $include_deposit_info = false)
    {
        $data = Donation::search(['id' => $donation_id, 'include_deposit_info' => $include_deposit_info]);
        if (!$data or !count($data)) {
            $data = Donation::search(['id' => $donation_id]); // Try finding the donation without deposit info.
            if (!$data or !count($data)) {
                return false;
            }
        }
        $data = $data[0];

        $this->id = $donation_id;
        $this->item = $this->find($donation_id);

        return $data;
    }

    public function add($data)
    {
        $donor = new Donor;
        $donor_id = $donor->findMatching([
            'donor_name' => $data['donor_name'],
            'donor_email'=> $data['donor_email'],
            'donor_phone'=> $data['donor_phone'],
            'donor_address'=> (!empty($data['donor_address']) ? $data['donor_address'] : ''),
        ], $data['fundraiser_user_id']);

        if (!$donor_id) {
            return $this->error("Can't find a valid Donor. Try logging out of the app and logging back in again.");
        }
        if (!$data['fundraiser_user_id']) {
            return $this->error("Can't find a valid Fundraiser. Try logging out of the app and logging back in again.");
        }
        if (!$data['type']) {
            return $this->error("Can't find a valid donation type. Try again later.");
        }

        if (!empty($data['added_on'])) {
            if ($data['added_on'] == '1970-01-01' or $data['added_on'] == '0000-00-00' or $data['added_on'] == '1970-01-01 0000-00-00' or !$data['added_on']) {
                $data['added_on'] = date("Y-m-d H:i:s");
            } else {
                $data['added_on'] = date("Y-m-d H:i:s", strtotime($data['added_on']));
            }
        } else {
            $data['added_on'] = date('Y-m-d H:i:s');
        }

        // Upload NACH form image
        $reference_file = '';
        if ($data['type'] == 'nach' and !is_string($data['reference_file']) and $data['reference_file']->isValid()) {
            $reference_file = $data['reference_file']->store('uploads');
        }

        $nach_start_on = (!empty($data['nach_start_on']) ? $data['nach_start_on'] : null);
        $nach_end_on = (!empty($data['nach_end_on']) ? $data['nach_end_on'] : null);

        if (!empty($data['donation_repeat_count']) and $data['donation_repeat_count']) {
            $donation_repeat_count = intval($data['donation_repeat_count']);
        } else {
            $nach_start_datetime = new DateTime($nach_start_on);
            $nach_end_datetime = new DateTime($nach_end_on);
            $diff = $nach_end_datetime->diff($nach_start_datetime);
            $diff_months = $diff->m + ($diff->y * 12);
            if (!$diff_months) {
                $diff_months = 1;
            }
            $donation_repeat_count = $diff_months;
        }
        
        $donation = Donation::create([
            'donor_id'          => $donor_id,
            'type'              => $data['type'],
            'fundraiser_user_id'=> $data['fundraiser_user_id'],
            'updated_by_user_id'=> $data['fundraiser_user_id'],
            'with_user_id'      => $data['fundraiser_user_id'],
            'amount'            => $data['amount'],
            'added_on'          => $data['added_on'],
            'updated_on'        => $data['added_on'],
            'reference_file'    => $reference_file,
            'nach_start_on'     => $nach_start_on,
            'nach_end_on'       => $nach_end_on,
            'donation_repeat_count' => $donation_repeat_count,
            'comment'           => (!empty($data['comment']) ? $data['comment'] : ''),
            'cheque_no'         => (!empty($data['cheque_no']) ? $data['cheque_no'] : ''),
            'status'            => 'collected',
        ]);

        if (!isset($data['dont_send_sms']) and ($data['type'] == 'cash' or $data['type'] == 'cheque')) { // This is an undocumented way to prevent sending SMS when making a donation. Useful for testing, seeding, etc.
            $sms = new SMS;
            $message = "Dear {$data['donor_name']}, Thanks a lot for your contribution of Rs. {$data['amount']} towards Make a Difference. "
                            . " This is only an acknowledgement. A confirmation and e-receipt would be sent once the amount reaches us.";
            $sms->send($data['donor_phone'], $message);
        }

        if (!isset($data['dont_send_email']) and ($data['type'] == 'cash' or $data['type'] == 'cheque')) { // This is an undocumented way to prevent sending Email when making a donation. Useful for testing, seeding, etc.

            $mail = new Email;
            $mail->from     = "noreply <noreply@makeadiff.in>";
            $mail->to       = $data['donor_email'];
            $mail->subject  = "Donation Acknowledgment";

            $base_path = app()->basePath();
            $base_url = url('/');

            $email_html = file_get_contents($base_path . '/resources/email_templates/donation_acknowledgement.html');
            $mail->html = str_replace(
                array('%BASE_FOLDER%','%BASE_URL%', '%AMOUNT%', '%DONOR_NAME%', '%DATE%'),
                array($base_path, $base_url,$data['amount'],$data['donor_name'], date('d/m/Y')),
                $email_html
            );

            $images = [
                $base_path . '/public/assets/mad-letterhead-left.png',
                $base_path . '/public/assets/mad-letterhead-logo.png',
                $base_path . '/public/assets/mad-letterhead-right.png'
            ];
            $mail->images = $images;
            $mail->send(); // $mail->queue();
        }

        return $donation;
    }

    public function approve($collected_from_user_id, $given_to_user_id, $send_email = 'send', $donation_id = false)
    {
        $this->chain($donation_id);
        $donation_id = $this->id;

        $this->edit([
            'status'            => 'collected',
            'with_user_id'      => $given_to_user_id,
            'updated_by_user_id'=> $given_to_user_id
        ]);

        ///  If national account does the approval, send recipt.
        if (($given_to_user_id == $this->national_account_user_id) and $send_email) {
            $this->edit(['status' => 'receipted']);

            $this->sendReceipt($send_email);
        }

        return true;
    }

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

    public function sendReceipt($send_email = 'send', $donation_id = false)
    {
        $this->chain($donation_id);
        if (!$donation_id) {
            $donation_id = $this->id;
        }
        
        // Don't send recipt for cash donations Greater than 2000 rs.
        if ($this->item->type == 'cash' and $this->item->amount > 2000) {
            return false;
        }

        $base_path = app()->basePath();
        $base_url = url('/');
        $donor = $this->item->donor()->first();

        $email_html = file_get_contents(base_path('resources/email_templates/donation_receipt.html'));
        $mail = new Email;
        $mail->from     = "noreply <noreply@makeadiff.in>";
        $mail->to       = $donor->email;
        $mail->subject  = "Donation Receipt";

        $replaces = [
            '%ASSETS_PATH%' => base_path('public/assets'),
            '%BASE_FOLDER%' => $base_path,
            '%BASE_URL%'    => $base_url,
            '%CREATED_AT%'  => date('dS M, Y h:i A', strtotime($this->item->added_on)),
            '%DATE%'        => date('d/m/Y'),
            '%AMOUNT%'      => $this->item->amount,
            '%AMOUNT_TEXT%' => $this->convertNumber($this->item->amount),
            '%DONATION_ID%' => $donation_id,
            '%DONOR_NAME%'  => $donor->name
        ];

        $mail->html = str_replace(array_keys($replaces), array_values($replaces), $email_html);

        $filename = $this->generateReceipt($donation_id);

        $mail->images = [
            $base_path . '/public/assets/mad-letterhead-left.png',
            $base_path . '/public/assets/mad-letterhead-logo.png',
            $base_path . '/public/assets/mad-letterhead-right.png',
        ];
        $mail->attachments = [$filename];

        if ($send_email == 'send') {
            $mail->send();
        } else {
            $mail->queue();
        }

        return true;
    }

    public function generateReceipt($donation_id = false)
    {
        $this->chain($donation_id);
        if (!$donation_id) {
            $donation_id = $this->id;
        }

        // :TODO: Generate only if status == 'receipted'. Don't want people to use this to fake the receipts.
        // Ideally this should only be run for donatations that are approved by finance team.

        $base_path = app()->basePath();
        $base_url = url('/');
        $donor = $this->item->donor()->first();
        
        // Generate PDF Receipt, attach it.
        // https://github.com/barryvdh/laravel-dompdf
        $pdf = app('dompdf.wrapper');
        $pdf_html = file_get_contents(base_path('resources/pdf_templates/donation_receipt.pdf.html'));

        $replaces = [
            '%ASSETS_PATH%' => base_path('public/assets'),
            '%BASE_URL%'    => $base_url,
            '%CREATED_AT%'  => date('dS M, Y h:i A', strtotime($this->item->added_on)),
            '%DATE%'        => date('d/m/Y'),
            '%AMOUNT%'      => $this->item->amount,
            '%AMOUNT_TEXT%' => $this->convertNumber($this->item->amount),
            '%DONATION_ID%' => $donation_id,
            '%DONOR_NAME%'  => $donor->name
        ];

        $pdf_html = str_replace(array_keys($replaces), array_values($replaces), $pdf_html);

        $pdf->loadHTML($pdf_html);
        $filename = 'Donation_Receipt_' . $donation_id . '.pdf';
        Storage::put($filename, $pdf->output());

        return base_path('storage/app/' . $filename);
    }

    /// Used to validate the donation
    public function validate($data)
    {
        $donor_address = '';
        extract($data);
        $donor = new Donor;

        // $donor_id = $this->findDonor($donor_name, $donor_email, $donor_phone, $donor_address);
        $donor_id = $donor->findMatching($data, $fundraiser_user_id);

        if (!$donor_id) {
            return \JSend::error("Can't find a valid Donor ID for this donation. Try logging out of the app and logging back in again.");
        }
        if (!$fundraiser_user_id) {
            return \JSend::error("Can't find a valid Fundraiser ID for this donation. Try logging out of the app and logging back in again.");
        }

        if ($this->checkIfDonorDetailsSameAsVolunteerBelowXAmount($donor_email, $donor_phone, $fundraiser_user_id)) {
            return \JSend::error("You seem to have entered your own details in place of the donor. If you continue, the donor won't receive the acknowledgement or receipt. You can only make two donations under your own details. You sure you want to continue?");
        } elseif ($created_date = $this->checkIfRepeatDonation($donor_id, $fundraiser_user_id, $amount)) { // = is used for assignment. It should NOT be ==
            return \JSend::error("Donation of Rs. $amount from $donor_name has already been added on $created_date. Are you sure you want to add the same amount again?");
        } elseif ($data = $this->checkIfRepeatDonationWithDifferentAmount($donor_id, $fundraiser_user_id)) {
            return \JSend::error("Donation of Rs. $data[amount] from $donor_name has already been added on $data[created_date]. Are you sure you want to add another amount again?");
        }

        return true;
    }
    private function checkIfDonorDetailsSameAsVolunteerBelowXAmount($donor_email, $donor_phone, $fundraiser_user_id)
    {
        $fundraiser = app('db')->table('User')->select('phone', 'email')->where('id', $fundraiser_user_id)->first();

        if (empty($fundraiser)) {
            return \JSend::fail("Can't find a valid Fundraiser ID for this donation. Try logging out of the app and logging back in again.");
        }

        if (($fundraiser->phone  == $donor_phone) || ($fundraiser->email == $donor_email)) {
            return true;
        } else {
            return false;
        }
    }

    private function checkIfRepeatDonation($donor_id, $fundraiser_user_id, $amount)
    {
        $donation = app('db')->table($this->table)->select('added_on')->where('donor_id', $donor_id)->where('fundraiser_user_id', $fundraiser_user_id)->where('amount', $amount)->first();

        if (!$donation) {
            return false;
        } else {
            $formatted_date = date('j-M-Y', strtotime($donation->added_on));
            return $formatted_date;
        }
    }

    private function checkIfRepeatDonationWithDifferentAmount($donor_id, $fundraiser_user_id)
    {
        $donation = app('db')->table($this->table)->select('added_on', 'amount')->where('donor_id', $donor_id)->where('fundraiser_user_id', $fundraiser_user_id)->first();

        if (!$donation) {
            return false;
        } else {
            $created_date = date('j-M-Y', strtotime($donation->added_on));
            $amount = $donation->amount;
            $return = compact("created_date", "amount");
            return $return;
        }
    }

    /**
    *  Function:  convertNumber
    *
    *  Description:
    *  Converts a given integer (in range [0..1T-1], inclusive) into
    *  alphabetical format ("one", "two", etc.)
    *
    *  @int
    *
    *  @return string
    *
    */
    private function convertNumber($number)
    {
        if (($number < 0) || ($number > 999999999)) {
            throw new Exception("Number is out of range");
        }

        $millions = floor($number / 1000000);  /* Millions (giga) */
        $number -= $millions * 1000000;
        $thousands = floor($number / 1000);     /* Thousands (kilo) */
        $number -= $thousands * 1000;
        $hundreds = floor($number / 100);      /* Hundreds (hecto) */
        $number -= $hundreds * 100;
        $tens = floor($number / 10);       /* Tens (deca) */
        $unit = $number % 10;               /* Ones */

        $res = "";

        if ($millions) {
            $res .= $this->convertNumber($millions) . " Million";
        }

        if ($thousands) {
            $res .= (empty($res) ? "" : " ") . $this->convertNumber($thousands) . " Thousand";
        }

        if ($hundreds) {
            $res .= (empty($res) ? "" : " ") . $this->convertNumber($hundreds) . " Hundred";
        }

        $ones = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen", "Nineteen"];
        $all_tens = ["", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", "Seventy", "Eigthy", "Ninety"];

        if ($tens || $unit) {
            if (!empty($res)) {
                $res .= " and ";
            }

            if ($tens < 2) {
                $res .= $ones[($tens * 10) + $unit];
            } else {
                $res .= $all_tens[$tens];

                if ($unit) {
                    $res .= "-" . $ones[$unit];
                }
            }
        }

        if (empty($res)) {
            $res = "zero";
        }

        return $res;
    }

    /// Delete the donation of which id is given.
    public function remove()
    {
        $args = func_get_args();

        $donation_id = $args[0];
        $deleter_id = 0;
        if (count($args) > 1) {
            $deleter_id = $args[1];
        }

        $this->chain($donation_id);

        $finance_team_user_ids = [170992, 163416, 104282, 148674, 1]; // These users can delete any Donation. Finance people. :HARDCODE:

        if ($deleter_id and !in_array($deleter_id, $finance_team_user_ids)) {
            $donations_for_deletion = $this->search(array('fundraiser_user_id' => $deleter_id))->merge($this->search(array('approver_user_id' => $deleter_id)));
            if (!$donations_for_deletion->count() or !$donations_for_deletion) {
                return JSend::fail("Can't find any donations that can be deleted by '$deleter_id'");
            }
            $donation_ids_for_deletion = $donations_for_deletion->filter(function ($val) {
                return $val->id;
            });

            if (!in_array($donation_id, $donation_ids_for_deletion)) {
                return JSend::fail("User $deleter_id can't delete the donation $donation_id");
            }
        }

        // Get a copy of the donation as backup
        $fields = $this->fillable;
        $query = "INSERT INTO Donut_Deleted_Donation(id,".implode(',', $fields).") ";
        foreach ($fields as $i => $value) {
            if ($value == 'updated_by_user_id') {
                $fields[$i] = '?';
            }
        } // Change the updated_by_user_id field to the deleter's ID.
        $query .= "SELECT id,".implode(',', $fields)." FROM Donut_Donation WHERE id=?";
        app('db')->insert($query, [$deleter_id, $donation_id]);
        
        return app('db')->delete("DELETE FROM Donut_Donation WHERE id=?", [$donation_id]); // Delete the donation.
    }
}
