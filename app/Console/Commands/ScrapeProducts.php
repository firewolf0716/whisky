<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ScrapeService;
use App\Services\StringService;
use App\Services\OtherFunc;
use App\Models\House;
use App\Models\Auction;

class ScrapeProducts extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'scrape:products';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Whisky! Products Scraper';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
      parent::__construct();
  }

  protected $imgsaved = 'products/';

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {

    //*scrape related data
    ScrapeService::scrapeBrands();
    ScrapeService::scrapeBottlers();
    ScrapeService::scrapeDistilleries();
    //********************

    //*scrape products
    $houses = House::all()->toArray();
    for ($i=0; $i < count($houses)-4; $i++) { 
      switch ($houses[$i]['site']) {
        case 'https://www.scotchwhiskyauctions.com/':
          ScrapeService::scrapeProducts1($houses[$i]);
          break;
        case 'https://www.whiskyauctioneer.com/':
          ScrapeService::scrapeProducts2($houses[$i]);
          break;
        // case 'Whisky.auction':
        //   var_dump($houses[$i]['name']);
        //   break;
        // case 'Just-whisky':
        //   var_dump($houses[$i]['name']);
        //   break;
        // case 'Whisky-onlineauctions':
        //   var_dump($houses[$i]['name']);
        //   break;
        // case 'Whiskyhammer':
        //   var_dump($houses[$i]['name']);
        //   break;        
        default:
          break;
      }  
    }
    //****************
  }

}
