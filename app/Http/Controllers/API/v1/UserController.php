<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;

class UserController extends Controller {

    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function index(\App\Services\SearchService $search_service) {
        // if data doesn't cache yet, dispatch the job to read and cache files data
        if (!empty(array_keys(config('data_providers.providers_searchable_keys'))) && !\Illuminate\Support\Facades\Cache::has('users.' . array_keys(config('data_providers.providers_searchable_keys'))[0])) {
            \App\Jobs\ReadProviderFilesData::dispatch();
            
            if ((!\App::runningUnitTests())) {
                return response()->json(
                                [
                                    'status' => true,
                                    'message' => __('we loading data, please retry after a while.')
                                ], 200
                );
            }
        }

        // call search service
        $result = $search_service->execute();

        return response()->json(
                        [
                            'status' => true,
                            'message' => __('done'),
                            'users' => $result,
                        ], 200
        );
    }

}
