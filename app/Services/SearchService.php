<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class SearchService {

    private $search_with_keys = null;
    private $status_code = null;
    private $providers_searchable_keys = null;
    private $query_string_keys = null;

    public function __construct() {
        $this->search_with_keys = config('data_providers.search_with_keys');
        $this->status_code = config('data_providers.status_code');
        $this->providers_searchable_keys = config('data_providers.providers_searchable_keys');
        $this->query_string_keys = array_keys(request()->query());
    }

    public function execute() {
        $result = [];

        // filter by provider name if a user searched by it else search in all data
        $result = $this->filterByProviderOrReadAllProviders($result);

        // filter by statusCode
        $result = $this->filterByStatusCode($result);

        // filter by balance range
        $result = $this->filterByBalance($result);

        // filter by currency
        $result = $this->filterByCurrency($result);

        if ($result && request()->hasAny($this->search_with_keys)) {
            return $result->values();
        }

        return $result;
    }

    // check if the provider has any param of params we trying to filter with, to search only in related data
    private function isProviderValidForSearch($provider) {
        return (Cache::has('users.' . $provider) && !request()->hasAny($this->search_with_keys)) || (Cache::has('users.' . $provider) && request()->hasAny($this->search_with_keys) && isset($this->providers_searchable_keys[$provider]) && array_intersect($this->query_string_keys, $this->providers_searchable_keys[$provider]));
    }

    // filter by provider name if a user searched by it else search in all data
    private function filterByProviderOrReadAllProviders($result) {
        $providers = array_keys($this->providers_searchable_keys);

        if (request()->has('provider') && !empty($providers) && in_array(request('provider'), $providers)) {
            if ($this->isProviderValidForSearch(request('provider'))) {
                $result = collect(Cache::get('users.' . request('provider')))->all();
            }
        } else {
            $users_data = [];
            if (!empty($providers)) {
                foreach ($providers as $provider) {
                    if ($this->isProviderValidForSearch($provider)) {
                        array_push($users_data, Cache::get('users.' . $provider));
                    }
                }
            }

            $result = collect($users_data)->values()->flatten(1)->all();
        }

        return $result;
    }

    // filter by statusCode
    private function filterByStatusCode($result) {
        //////////////  recommended to convert all related keys to be the same in files (in real case)  //////////////
        if (request()->has('statusCode') && !empty($this->status_code)) {
            $result = collect($result)->filter(function ($item) {
                return ((isset($item['status']) && isset($this->status_code[request('statusCode')]) && in_array($item['status'], $this->status_code[request('statusCode')])) || (isset($item['statusCode']) && isset($this->status_code[request('statusCode')]) && in_array($item['statusCode'], $this->status_code[request('statusCode')])));
            });
        }

        return $result;
    }

    // filter by balance range
    private function filterByBalance($result) {
        if (request()->has('balanceMin') && request()->has('balanceMax')) {
            $result = collect($result)->whereBetween('balance', [request('balanceMin'), request('balanceMax')]);
        }
        return $result;
    }

    // filter by currency
    private function filterByCurrency($result) {
        if (request()->has('currency') && (collect($result)->contains('currency', request('currency')) || collect($result)->contains('Currency', request('currency')) )) {
            $result = collect($result)->filter(function ($item) {
                return ((isset($item['currency']) && $item['currency'] == request('currency')) || (isset($item['Currency']) && $item['Currency'] == request('currency')));
            });
        }
        return $result;
    }

}
