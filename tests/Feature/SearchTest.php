<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase {

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetAllUsers() {
        $response = $this->get('/api/v1/users');
        $response->assertStatus(200)->json('users');
    }

    public function testGetAllUsersFromProviderX() {
        $response = $this->get('/api/v1/users?provider=DataProviderX');

        // result exist in DataProviderX file
        $response->assertStatus(200)->assertJsonFragment([
            "parentAmount" => 280,
            "Currency" => "EUR",
            "parentEmail" => "parent1@parent.eu",
            "statusCode" => 1,
            "registerationDate" => "2018-11-30",
            "parentIdentification" => "d3d29d70-1d25-11e3-8591-034165a3a613"
        ]);
    }

    public function testGetAllUsersFromProviderY() {
        $response = $this->get('/api/v1/users?provider=DataProviderY');

        // result exist in DataProviderY file
        $response->assertStatus(200)->assertJsonFragment([
            "balance" => 354.5,
            "currency" => "AED",
            "email" => "parent100@parent.eu",
            "status" => 100,
            "created_at" => "22/12/2018",
            "id" => "3fc2-a8d1"
        ]);
    }

    public function testGetUsersFilteredByCurrency() {
        $response = $this->get('/api/v1/users?currency=USD');

        $response->assertStatus(200)->assertJsonFragment([
            "parentAmount" => 200.5,
            "Currency" => "USD",
            "parentEmail" => "parent2@parent.eu",
            "statusCode" => 2,
            "registerationDate" => "2018-01-01",
            "parentIdentification" => "e3rffr-1d25-dddw-8591-034165a3a613"
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            "balance" => 222,
            "currency" => "USD",
            "email" => "parent400@parent.eu",
            "status" => 300,
            "created_at" => "11/11/2018",
            "id" => "sfc2-e8d1"
        ]);
    }

    public function testGetUsersFilteredByStatusCode() {
        $response = $this->get('/api/v1/users?statusCode=decline');

        $response->assertStatus(200)->assertJsonFragment([
            "balance" => 130,
            "currency" => "EUR",
            "email" => "parent500@parent.eu",
            "status" => 200,
            "created_at" => "02/08/2019",
            "id" => "4fc3-a8d2"
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            "parentAmount" => 200.5,
            "Currency" => "USD",
            "parentEmail" => "parent2@parent.eu",
            "statusCode" => 2,
            "registerationDate" => "2018-01-01",
            "parentIdentification" => "e3rffr-1d25-dddw-8591-034165a3a613"
        ]);
    }

    public function testGetUsersFilteredByBalance() {
        $response = $this->get('/api/v1/users?balanceMin=130&balanceMax=354.5');

        // balance equal balanceMin
        $response->assertStatus(200)->assertJsonFragment([
            "balance" => 130,
            "currency" => "EUR",
            "email" => "parent500@parent.eu",
            "status" => 200,
            "created_at" => "02/08/2019",
            "id" => "4fc3-a8d2"
        ]);

        // balance between range
        $response->assertStatus(200)->assertJsonFragment([
            "balance" => 222,
            "currency" => "USD",
            "email" => "parent400@parent.eu",
            "status" => 300,
            "created_at" => "11/11/2018",
            "id" => "sfc2-e8d1"
        ]);

        // balance equal balanceMax
        $response->assertStatus(200)->assertJsonFragment([
            "balance" => 354.5,
            "currency" => "AED",
            "email" => "parent100@parent.eu",
            "status" => 100,
            "created_at" => "22/12/2018",
            "id" => "3fc2-a8d1"
        ]);
    }

    public function testGetUsersFilteredByAllParams() {
        $response = $this->get('/api/v1/users?balanceMin=130&balanceMax=550&currency=AED&statusCode=authorised');

        $response->assertStatus(200)->assertJsonFragment([
            "balance" => 354.5,
            "currency" => "AED",
            "email" => "parent100@parent.eu",
            "status" => 100,
            "created_at" => "22/12/2018",
            "id" => "3fc2-a8d1"
        ]);
    }

    public function testGetUsersFilteredByAllParamsAndProvider() {
        $response = $this->get('/api/v1/users?provider=DataProviderY&balanceMin=130&balanceMax=354.5&currency=AED&statusCode=authorised');

        $response->assertStatus(200)->assertJsonFragment([
            "balance" => 354.5,
            "currency" => "AED",
            "email" => "parent100@parent.eu",
            "status" => 100,
            "created_at" => "22/12/2018",
            "id" => "3fc2-a8d1"
        ]);
    }

    public function testGetAllUsersFromProviderNotExist() {
        $response = $this->get('/api/v1/users?provider=DataProviderZ');
        $response->assertStatus(200);
    }

    public function testGetUsersFilteredByBalanceAndProviderDosntHasBalanceAttribute() {
        $response = $this->get('/api/v1/users?provider=DataProviderX&balanceMin=130&balanceMax=354.5');
        $response->assertStatus(200)->assertJsonFragment([]);
    }

    public function testGetUsersFilteredByParamsOutOfScope() {
        $response = $this->get('/api/v1/users?param=1');
        $response->assertStatus(200);

        $response = $this->get('/api/v1/users?statusCode=declineee&currency=USD');
        $response->assertStatus(200);

        $response = $this->get('/api/v1/users?statusCode=declineee');
        $response->assertStatus(200);

        $response = $this->get('/api/v1/users?status=declineee');
        $response->assertStatus(200);

        $response = $this->get('/api/v1/users?status=decline');
        $response->assertStatus(200);
    }

}
