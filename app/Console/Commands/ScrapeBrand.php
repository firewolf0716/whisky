<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ScrapeService;

class ScrapeBrand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:brands';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Whisky! Brands Scraper';

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
        ScrapeService::scrapeBrands();
        return true;
    }
}
