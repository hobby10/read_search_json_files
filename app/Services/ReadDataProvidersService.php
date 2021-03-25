<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ReadDataProvidersService {

    public function execute() {
        $data_files_folder = config('data_providers.data_files_folder');
        $all_files = Storage::disk('local_dataprovider')->files($data_files_folder);
        foreach ($all_files as $file) {
            if (Storage::disk('local_dataprovider')->exists($file)) {
                // get file name without extension
                $file_name_key = str_replace('.json', '', str_replace($data_files_folder . '/', '', $file));
                // read file
                $json_data = $this->readDataFromJsonFile($file);
                if ($json_data && isset($json_data->toArray()['users'])) {
                    // add data to cache
                    \Illuminate\Support\Facades\Cache::put('users.' . $file_name_key, $json_data->toArray()['users']);
                }
            }
        }
    }

    // https://github.com/salsify/jsonstreamingparser
    // Streaming parser for parsing very large JSON documents to avoid loading the entire thing into memory
    private function readDataFromJsonFile($file) {
        $listener = new \JsonStreamingParser\Listener\InMemoryListener();
        $stream = fopen(storage_path($file), 'r');
        try {
            $parser = new \JsonStreamingParser\Parser($stream, $listener);
            $parser->parse();


            fclose($stream);
        } catch (Exception $e) {
            fclose($stream);
            throw $e;
        }

        // use lazy collection
        $json_data = collect($listener->getJson())->lazy();
//        $json_data = collect($listener->getJson());

        return $json_data;
    }

}
