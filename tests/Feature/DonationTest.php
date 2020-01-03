<?php
namespace Tests\Feature;

use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class DonationTest extends TestCase
{
    private $user_id = 1;

    /// Path: GET    /donations
    public function testGetDonationsList()
    {
        // if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/users/' . $this->user_id . '/donations');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->response->assertStatus(200);

        $found = true;

        // SQL get all donations made by user in current year, make sure all donations are here.
        $q = app('db')->table('Donut_Donation');
        $q->where('fundraiser_user_id', $this->user_id)->where('added_on', '>=', "{$this->year}-05-01 00:00:00");
        $donations_this_year = $q->pluck('id')->toArray();

        foreach ($data->data->donations as $key => $info) {
            if(!in_array($info->id, $donations_this_year)) {
                $found = false;
                break;
            }
        }
        
        $this->assertTrue($found);
    }

    /// Path: GET    /donations?fundraiser_user_id=1&deposited=false
    public function testGetUndepositedDonations()
    {
        // if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load("/donations?fundraiser_user_id={$this->user_id}&deposited=false");
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->response->assertStatus(200);
        
        $q = app('db')->table('Donut_Donation AS D');
        $q->where('D.fundraiser_user_id', $this->user_id)->where('D.added_on', '>=', "{$this->year}-05-01 00:00:00");
        $donations_this_year = $q->pluck('D.id')->toArray();

        $q = app('db')->table("Donut_Deposit AS DP");
        $q->join("Donut_DonationDeposit AS DD", 'DD.deposit_id', '=', 'DP.id');
        $q->where('DP.collected_from_user_id', $this->user_id);
        $q->whereIn("DD.donation_id", $donations_this_year);
        $q->whereIn("DP.status", ['approved', 'pending']);
        $depsoited_donations_this_year = $q->pluck('DD.donation_id')->toArray();

        $found = true;
        foreach ($data->data->donations as $key => $info) {
            if(!in_array($info->id, $donations_this_year)) { // It should be in this list.
                $found = false;
                break;
            }

            // AND should NOT be in this list - this is deposited donations.
            if(in_array($info->id, $depsoited_donations_this_year)) {
                $found = false;
                break;
            }            
        }

        $this->assertTrue($found);
    }

    /// Path: GET    /donations/{donation_id}
    public function testGetSingleDonation()
    {
        // if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $donation_id = 24722;
        $this->load("/donations/$donation_id");
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->response->assertStatus(200);
        
        $q = app('db')->table('Donut_Donation');
        $q->where('id', $donation_id);
        $donation_info = $q->first();

        $this->assertEquals($data->data->donation->id, $donation_id);
        $this->assertEquals($data->data->donation->amount, $donation_info->amount);
        $this->assertEquals($data->data->donation->fundraiser_user_id, $donation_info->fundraiser_user_id);
    }

}
