<?php

namespace Sebastienheyd\BoilerplateMediaManager\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Sebastienheyd\BoilerplateMediaManager\Models\Path;

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
    protected $description = 'Clear all boilerplate media manager images thumbs';

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

        $nb = 0;

        Collection::make($storage->allFiles())->filter(function ($item) use ($storage, &$nb) {
            if (preg_match('`/thumb_.*?$`', $item)) {
                $storage->delete(preg_replace('#^public/#', '', $item));
                $nb++;
            }
        });

        $this->info($nb.' thumb'.($nb > 1 ? 's' : '').' deleted');

        $storage->deleteDirectory('thumbs');

        $this->info('thumbs directory deleted');

        Collection::make($storage->allDirectories())->filter(function ($item) use ($storage) {
            if (!preg_match('`^thumbs`', $item)) {
                $path = new Path('/'.$item);
                $path->clearCache();
            }
        });
    }
}
