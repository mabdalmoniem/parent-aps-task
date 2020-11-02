<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\Log;

class DataProvider
{
    private $data;
    private $config;

    public function __construct()
    {
        $this->data = collect();
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }

    public function setInitialData($potential_data)
    {
        try {

            // if $potential_data is a JSON string
            if (!is_array($potential_data)) {
                $potential_data = json_decode($potential_data, true);

                if (isset($potential_data['users'])) {
                    $potential_data = $potential_data['users'];
                }
            }

            $this->data = collect($potential_data);
        } catch (Exception $e) {
            $this->data = collect();
            Log::error('An error occurred while setting initial data', [
                'message' => $e->getMessage(),
                'payload' => $potential_data
            ]);
        }

        return $this;
    }

    public function filterByCurrency(string $currency = null): self
    {
        if (empty($currency)) return $this;

        $this->data = $this->data->filter(function ($item) use ($currency) {
            $key = $this->config['mappings']['currency'];               // 'currency' or 'Currency'

            if (!isset($item[$key])) return true;                       // if key isn't there, skip

            return $item[$key] == strtoupper($currency);                // return if $item['currency'] == $currency
        });

        return $this;
    }

    public function filterByAmount(string $operator, $amount = null): self
    {
        if (empty($operator) || empty($amount)) return $this;

        $amount = floatval($amount);

        $this->data = $this->data->filter(function ($item) use ($amount, $operator) {
            $key = $this->config['mappings']['amount'];             // 'parentAmount' or 'balance'

            if (!isset($item[$key])) return true;

            if ($operator == '>=') {
                return $item[$key] >= $amount;
            } else {
                return $item[$key] <= $amount;
            }
        });

        return $this;
    }

    public function filterByStatus(string $status = null): self
    {
        $status = strtolower($status);

        if (empty($status) || !in_array($status, ['authorized', 'declined', 'refunded'])) return $this;

        $this->data = $this->data->filter(function ($item) use ($status) {
            $key = $this->config['mappings']['status'];                             // 'status' or 'statusCode'

            if (!isset($item[$key])) return true;                                   // if key isn't there, skip

            if (!isset($this->config['mappings']['codes'][$status])) return true;   // if the code isn't registered, skip

            return $item[$key] == $this->config['mappings']['codes'][$status];
        });

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public static function fetchData($key)
    {
        $data = [
            'DataProviderX' => '{"users":[{"parentAmount":280,"Currency":"EUR","parentEmail":"parent1@parent.eu","statusCode":1,"registrationDate":"2018-11-30","parentIdentification":"d3d29d70-1d25-11e3-8591-034165a3a613"},{"parentAmount":200.5,"Currency":"USD","parentEmail":"parent2@parent.eu","statusCode":2,"registrationDate":"2018-01-01","parentIdentification":"e3rffr-1d25-dddw-8591-034165a3a613"},{"parentAmount":500,"Currency":"EGP","parentEmail":"parent3@parent.eu","statusCode":1,"registrationDate":"2018-02-27","parentIdentification":"4erert4e-2www-wddc-8591-034165a3a613"},{"parentAmount":400,"Currency":"AED","parentEmail":"parent4@parent.eu","statusCode":1,"registrationDate":"2019-09-07","parentIdentification":"d3dwwd70-1d25-11e3-8591-034165a3a613"},{"parentAmount":200,"Currency":"EUR","parentEmail":"parent5@parent.eu","statusCode":1,"registrationDate":"2018-10-30","parentIdentification":"d3d29d40-1d25-11e3-8591-034165a3a6133"}]}',
            'DataProviderY' => '{"users":[{"balance":354.5,"currency":"AED","email":"parent100@parent.eu","status":100,"created_at":"22/12/2018","id":"3fc2-a8d1"},{"balance":1000,"currency":"USD","email":"parent200@parent.eu","status":100,"created_at":"22/12/2018","id":"4fc2-a8d1"},{"balance":560,"currency":"AED","email":"parent300@parent.eu","status":200,"created_at":"22/12/2018","id":"rrc2-a8d1"},{"balance":222,"currency":"USD","email":"parent400@parent.eu","status":300,"created_at":"11/11/2018","id":"sfc2-e8d1"},{"balance":130,"currency":"EUR","email":"parent500@parent.eu","status":200,"created_at":"02/08/2019","id":"4fc3-a8d2"}]}'
        ];

        if (!isset($data[$key])) return [];

        return $data[$key];
    }
}
