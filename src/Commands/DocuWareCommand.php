<?php

namespace codebar\DocuWare\Commands;

use Illuminate\Console\Command;

class DocuWareCommand extends Command
{
    public $signature = 'laravel-docuware';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
