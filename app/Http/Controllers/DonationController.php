<?php
namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Online_Donation;
use Illuminate\Http\Request;
use JSend;

class DonationController extends Controller
{
    // This will merge both Donut Donations and Online Donations into one result.
    public function search($search)
    {
        // $search_fields = ['from', 'to', 'fundraiser_user_id', 'amount'];

        $online_donation = new Online_Donation;
        $online = $online_donation->search($search)->get();

        $online->map(function($don) {
            $don->type = 'online_donation';
            $don->status = 'receipted';
            return $don;
        });

        $donut_donation = new Donation;

        // If someone searches specifically for 'all', don't filter by donation type. This will be needed for getting historic donation data.
        if(isset($search['type'])) {
            if($search['type'] == 'all') {
                unset($search['type']);
            }
        } else { // If no type is specified, use the 'crowdfunding_patforms' - for 2020 and forward.
            $search['type'] = 'crowdfunding_patforms';            
        }
        $donuts = $donut_donation->baseSearch($search)->get();

        $all_donations = $online->merge($donuts)->sortByDesc('added_on')->all();

        return array_values($all_donations); // If array_values is not there, it will preserve the key after sorting. Array index will be something like 3,0,2,1.
    }
}
