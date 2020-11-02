<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    // create a data provider
    // load data according to requests (if no provider given, load all data. else choose a provider)
    // filter the data set by 

    $x_data = json_decode('{"users":[{"parentAmount":280,"Currency":"EUR","parentEmail":"parent1@parent.eu","statusCode":1,"registrationDate":"2018-11-30","parentIdentification":"d3d29d70-1d25-11e3-8591-034165a3a613"},{"parentAmount":200.5,"Currency":"USD","parentEmail":"parent2@parent.eu","statusCode":2,"registrationDate":"2018-01-01","parentIdentification":"e3rffr-1d25-dddw-8591-034165a3a613"},{"parentAmount":500,"Currency":"EGP","parentEmail":"parent3@parent.eu","statusCode":1,"registrationDate":"2018-02-27","parentIdentification":"4erert4e-2www-wddc-8591-034165a3a613"},{"parentAmount":400,"Currency":"AED","parentEmail":"parent4@parent.eu","statusCode":1,"registrationDate":"2019-09-07","parentIdentification":"d3dwwd70-1d25-11e3-8591-034165a3a613"},{"parentAmount":200,"Currency":"EUR","parentEmail":"parent5@parent.eu","statusCode":1,"registrationDate":"2018-10-30","parentIdentification":"d3d29d40-1d25-11e3-8591-034165a3a6133"}]}', true);

    $y_data = json_decode('{"users":[{"balance":354.5,"currency":"AED","email":"parent100@parent.eu","status":100,"created_at":"22/12/2018","id":"3fc2-a8d1"},{"balance":1000,"currency":"USD","email":"parent200@parent.eu","status":100,"created_at":"22/12/2018","id":"4fc2-a8d1"},{"balance":560,"currency":"AED","email":"parent300@parent.eu","status":200,"created_at":"22/12/2018","id":"rrc2-a8d1"},{"balance":222,"currency":"USD","email":"parent400@parent.eu","status":300,"created_at":"11/11/2018","id":"sfc2-e8d1"},{"balance":130,"currency":"EUR","email":"parent500@parent.eu","status":200,"created_at":"02/08/2019","id":"4fc3-a8d2"}]}', true);

    $result = collect(array_merge($x_data['users'], $y_data['users']));

    $data_providers = array_keys(config('data-providers'));

    collect($data_providers)
        ->each(function ($provider) use (&$result, $x_data, $y_data) {

            if (!empty(request()->get('currency'))) {
                $result = $result->filter(function ($item) use ($provider) {
                    $key = "data-providers.{$provider}.mappings.currency";
                    if (!isset($item[config($key)])) return true;
                    return $item[config($key)] == strtoupper(request()->get('currency'));
                });
            }

            if (!empty(request()->get('balanceMin'))) {
                $result = $result->filter(function ($item) use ($provider) {
                    $key = "data-providers.{$provider}.mappings.amount";
                    if (!isset($item[config($key)])) return true;
                    return $item[config($key)] >= request()->get('balanceMin');
                });
            }

            if (!empty(request()->get('balanceMax'))) {
                $result = $result->filter(function ($item) use ($provider) {
                    $key = "data-providers.{$provider}.mappings.amount";
                    if (!isset($item[config($key)])) return true;
                    return $item[config($key)] <= request()->get('balanceMax');
                });
            }


            // $result = $result->when(!empty(request()->get('statusCode')), function () use ($result) {
            //     return $result->filter(fn ($item) => $item[config('data-providers.DataProviderX.mappings.status')] == 1);
            // });

        });


    return response()->json([
        'data' => $result->values()
    ]);
});
