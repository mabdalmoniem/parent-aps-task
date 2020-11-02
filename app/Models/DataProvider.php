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

    public static function fetchData($config)
    {
        if (!isset($config['database_path'])) return [];

        try {
            return file_get_contents(database_path() . $config['database_path']);
        } catch (Exception $e) {
            Log::error('An error occurred while fetching the data from the data source', [
                'message' => $e->getMessage()
            ]);
        }
        return [];
    }
}
