<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataProvider;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $providers = collect(config('data-providers'))
            ->filter(function ($provider) {
                return $provider['enabled'];
            })
            ->reject(function ($config, $provider_name) use ($request) {
                return !empty($request->get('provider')) && $request->get('provider') != $provider_name;
            });

        $result = collect();

        $providers->each(function ($config, $provider_name) use (&$result, $request) {
            $provider = (new DataProvider)
                ->setConfig($config)
                ->setInitialData(DataProvider::fetchData($config))
                ->filterByStatus($request->get('status'))
                ->filterByCurrency($request->get('currency'))
                ->filterByAmount('>=', $request->get('balanceMin'))
                ->filterByAmount('<=', $request->get('balanceMax'));

            $result = $result->merge($provider->getData());
        });

        return response()->json([
            'data' => $result
        ]);
    }
}
