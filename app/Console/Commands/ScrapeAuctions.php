<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ScrapeService;

class ScrapeAuctions extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'scrape:auctions';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Whisky! Auctions Scraper';

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
    $house = config('scrape.houses')[0];
    ScrapeService::scrapeProducts($house);
    return true;  
  }

}
