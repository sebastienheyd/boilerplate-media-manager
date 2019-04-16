<?php

namespace Sebastienheyd\BoilerplateMediaManager\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class Clearthumbs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thumbs:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all boilerplate media manager images cache';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $storage = Storage::disk('public');

        if ($storage->exists(config('mediamanager.thumbs_dir'))) {
            $storage->deleteDirectory(config('mediamanager.thumbs_dir'));
            $this->info('Folder '.config('mediamanager.thumbs_dir').' has been cleared');
        }
    }
}
