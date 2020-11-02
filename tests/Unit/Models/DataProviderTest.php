<?php

namespace Tests\Unit\Models;

use App\Models\DataProvider;
use Exception;
use Tests\TestCase;

class DataProviderTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function it_can_set_initial_data()
    {
        // arrange
        $provider = new DataProvider;
        $this->assertCount(0, $provider->getData());

        // act
        $provider->setInitialData(DataProvider::fetchData('DataProviderX'));

        // assert
        $this->greaterThan(0, $provider->getData());
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_set_initial_data_with_array()
    {
        // arrange
        $provider = new DataProvider;
        $data = [
            [
                "balance" => 560,
                "currency" => "AED",
                "email" => "parent300@parent.eu",
                "status" => 200,
                "created_at" => "22/12/2018",
                "id" => "rrc2-a8d1"
            ]
        ];

        // act
        $provider->setInitialData($data);

        // assert
        $this->assertEquals(1, $provider->getData()->count());
        $this->assertEquals($data, $provider->getData()->toArray());
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_set_initial_data_with_json_string()
    {
        // arrange
        $provider = new DataProvider;
        $data = '[{"parentAmount":280,"Currency":"EUR","parentEmail":"parent1@parent.eu","statusCode":1,"registrationDate":"2018-11-30","parentIdentification":"d3d29d70-1d25-11e3-8591-034165a3a613"}]';

        // act
        $provider->setInitialData($data);

        // assert
        $this->assertEquals(1, $provider->getData()->count());
        $this->assertEquals(json_decode($data, true), $provider->getData()->toArray());
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_set_initial_data_with_json_string_with_users_key()
    {
        // arrange
        $provider = new DataProvider;
        $data = '{"users":[{"parentAmount":280,"Currency":"EUR","parentEmail":"parent1@parent.eu","statusCode":1,"registrationDate":"2018-11-30","parentIdentification":"d3d29d70-1d25-11e3-8591-034165a3a613"}]}';

        // act
        $provider->setInitialData($data);

        // assert
        $this->assertEquals(1, $provider->getData()->count());
        $this->assertEquals(json_decode($data, true)['users'], $provider->getData()->toArray());
    }


    /**
     * @test
     *
     * @return void
     */
    public function it_sets_malformed_json_into_empty_collection()
    {
        // arrange
        $provider = new DataProvider;
        $data = 'WRONG_JSON_FORMAT';

        // act
        $provider->setInitialData($data);

        // assert
        $this->assertEquals(0, $provider->getData()->count());
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_set_config()
    {
        // arrange
        $provider = new DataProvider;
        $this->assertNull($provider->getConfig());

        // act
        $provider->setConfig(config('data-providers')['DataProviderX']);

        // assert
        $this->assertNotNull($provider->getConfig());
        $this->assertEquals(config('data-providers')['DataProviderX'], $provider->getConfig());
    }


    /**
     * @test
     *
     * @return void
     */
    public function it_can_be_filtered_by_currency()
    {
        // arrange
        $provider = new DataProvider;
        $provider->setInitialData([
            ['Currency' => 'USD'],
            ['Currency' => 'EGP'],
            ['Currency' => 'USD'],
        ]);
        $provider->setConfig(config('data-providers')['DataProviderX']);
        $this->assertCount(3, $provider->getData());

        // act
        $provider->filterByCurrency('usd');

        // assert
        $this->assertCount(2, $provider->getData());
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_be_filtered_by_status()
    {
        // arrange
        $provider = new DataProvider;
        $provider->setInitialData([
            ['statusCode' => '1'],
            ['statusCode' => '2'],
            ['statusCode' => '3'],
        ]);
        $provider->setConfig(config('data-providers')['DataProviderX']);
        $this->assertCount(3, $provider->getData());

        // act
        $provider->filterByStatus('declined');

        // assert
        $this->assertCount(1, $provider->getData());
    }


    /**
     * @test
     *
     * @return void
     */
    public function it_can_be_filtered_by_amount_range()
    {
        // arrange
        $provider = new DataProvider;
        $provider->setInitialData([
            ['parentAmount' => 1],
            ['parentAmount' => 100],
            ['parentAmount' => 200],
            ['parentAmount' => 300],
            ['parentAmount' => 500],
        ]);
        $provider->setConfig(config('data-providers')['DataProviderX']);
        $this->assertCount(5, $provider->getData());

        // act
        $provider->filterByAmount('>=', 0);
        $provider->filterByAmount('<=', 100);

        // assert
        $this->assertCount(2, $provider->getData());
    }
}
