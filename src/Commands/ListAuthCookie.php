<?php

namespace CodebarAg\DocuWare\Commands;

use CodebarAg\DocuWare\Support\Auth;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class ListAuthCookie extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docuware:list-auth-cookie {--with-date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List DocuWare Auth Cookie';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Auth::check();

        $cacheKey = Auth::CACHE_KEY;
        $cookie = Auth::cookies();
        $cookieHash = Arr::get($cookie, Auth::COOKIE_NAME);
        $cookieCreationDate = Arr::get($cookie, 'CreatedAt');

        if (! $cookieHash) {
            $this->info('No cookie found for the Key "'.$cacheKey.'".');
        }
        if ($cookieHash) {
            $this->info($cookieHash);
            if ($this->option('with-date')) {
                $this->newLine();
                $this->info("Created At: {$cookieCreationDate}");
            }
        }

        return Command::SUCCESS;
    }
}
