<?php

namespace Tests\Feature\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function it_returns_the_expected_structure()
    {
        // act
        $response = $this->get(route('api.users'));

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
        $response->assertJsonFragment(
            [
                "parentAmount" => 280,
                "Currency" => "EUR",
                "parentEmail" => "parent1@parent.eu",
                "statusCode" => 1,
                "registrationDate" => "2018-11-30",
                "parentIdentification" => "d3d29d70-1d25-11e3-8591-034165a3a613"
            ]
        );
        $response->assertJsonFragment(
            [
                "balance" => 560,
                "currency" => "AED",
                "email" => "parent300@parent.eu",
                "status" => 200,
                "created_at" => "22/12/2018",
                "id" => "rrc2-a8d1"
            ]
        );
        $response->assertJsonCount(10, 'data');
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_returns_only_the_selected_provider_data()
    {
        // act
        $response = $this->get(route('api.users', [
            'provider' => 'DataProviderX'
        ]));

        // assert
        $response->assertJsonCount(5, 'data');
        $response->assertJsonFragment([
            "parentEmail" => "parent1@parent.eu"
        ]);
        $response->assertJsonMissing([
            "email" => "parent300@parent.eu",
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_returns_empty_json_array_when_choosing_wrong_provider()
    {
        // act
        $response = $this->get(route('api.users', [
            'provider' => 'UNKNOWN_PROVIDER'
        ]));

        // assert
        $response->assertJsonCount(0, 'data');
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_be_filtered_by_currency()
    {
        // act
        $unfiltered_response = $this->get(route('api.users'));
        $filtered_response = $this->get(route(
            'api.users',
            [
                'currency' => 'USD'
            ]
        ));

        // assert
        $this->assertLessThanOrEqual(
            count($unfiltered_response['data']),
            count($filtered_response['data'])
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_returns_empty_json_array_when_choosing_wrong_currency()
    {
        // act
        $unfiltered_response = $this->get(route('api.users'));
        $filtered_response = $this->get(route(
            'api.users',
            [
                'currency' => 'WRONG_CURRENCY'
            ]
        ));

        // assert
        $filtered_response->assertJsonCount(0, 'data');
        $this->assertLessThan(
            count($unfiltered_response['data']),
            count($filtered_response['data'])
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_be_filtered_by_amount()
    {
        // act
        $unfiltered_response = $this->get(route('api.users'));
        $filtered_with_min_response = $this->get(route(
            'api.users',
            [
                'balanceMin' => 200
            ]
        ));
        $filtered_with_max_response = $this->get(route(
            'api.users',
            [
                'balanceMax' => 200
            ]
        ));

        // assert
        $this->assertLessThanOrEqual(
            count($unfiltered_response['data']),
            count($filtered_with_min_response['data'])
        );
        $this->assertLessThanOrEqual(
            count($unfiltered_response['data']),
            count($filtered_with_max_response['data'])
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_be_filtered_by_amount_range()
    {
        // act
        $unfiltered_response = $this->get(route('api.users'));
        $filtered_with_range_response = $this->get(route(
            'api.users',
            [
                'balanceMin' => 0,
                'balance_max' => 100
            ]
        ));

        // assert
        $this->assertLessThanOrEqual(
            count($unfiltered_response['data']),
            count($filtered_with_range_response['data'])
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_be_filtered_by_status()
    {
        // act
        $unfiltered_response = $this->get(route('api.users'));
        $filtered_response = $this->get(route(
            'api.users',
            [
                'status' => 'refunded'
            ]
        ));

        // assert
        $this->assertLessThanOrEqual(
            count($unfiltered_response['data']),
            count($filtered_response['data'])
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_be_filtered_by_multiple_filters()
    {
        // act
        $unfiltered_response = $this->get(route('api.users'));
        $filtered_response = $this->get(route(
            'api.users',
            [
                'status' => 'refunded',
                'balanceMin' => 0,
                'balance_max' => 100,
                'currency' => 'usd'
            ]
        ));

        // assert
        $this->assertGreaterThanOrEqual(
            1,
            count($filtered_response['data'])
        );
        $this->assertLessThanOrEqual(
            count($unfiltered_response['data']),
            count($filtered_response['data'])
        );
    }
}
