<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReadProviderFilesData extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:read_provider_files_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read Provider Files Data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        \App\Jobs\ReadProviderFilesData::dispatch();
    }

}
