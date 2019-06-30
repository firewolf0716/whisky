<?php

namespace App\Services;

use Goutte;
use DB;
use App\Models\Brand;
use App\Models\Bottler;
use App\Models\Distillery; 
use App\Models\Auction; 
use App\Models\Product; 
use App\Models\ProductImg; 
use App\Services\OtherFunc;
use App\Services\StringService;

class ScrapeService
{
  public static function scrapeBrands()
  {   
    $url = env('WHISKY_BASE_URL') . '/whiskies/brands';
    print_r("\n" . 'Starting brand scraper...');        
    $crawler = Goutte::request('GET', $url);
    print_r("\n" . 'Filtering brands scraped data...');  
    $crawler = $crawler->filter('.whiskytable tbody tr');

    $crawler->each(function ($node) {
      //get brands data
      $brand = $node->filter('td')->eq(0)->filter('a')->text();
      $country = $node->filter('td')->eq(1)->text();
      if (empty($country)) $country = null;
      $whiskies = $node->filter('td')->eq(2)->text();
      if (empty($whiskies)) $whiskies = null;
      $votes = $node->filter('td')->eq(3)->text();
      if (empty($votes)) $votes = null;
      $rating = $node->filter('td')->eq(4)->text();
      if (empty($rating)) $rating = null;
      $wbranking = $node->filter('td')->eq(5)->text();
      if (empty($wbranking)) $wbranking = null;

      //check if finished && new auction
      if (!Brand::IsExist($brand)) {
          //insert to database
          $nbrand = new Brand();
          $nbrand->brand = $brand;
          $nbrand->country = $country;
          $nbrand->whiskies = $whiskies;
          $nbrand->votes = $votes;
          $nbrand->rating = $rating;
          $nbrand->wbranking = $wbranking;
          $nbrand->save();
      }
          
    });
    print_r("\n" . 'Finished saving data.');  
    print_r("\n" . 'Got brands. ');  
    print_r("\n" . 'Done!'); 
  }

  public static function scrapeBottlers()
  {   
    $url = env('WHISKY_BASE_URL') . '/whiskies/bottlers';
    print_r("\n" . 'Starting bottler scraper...');        
    $crawler = Goutte::request('GET', $url);
    print_r("\n" . 'Filtering scraped bottler data...');  
    $crawler = $crawler->filter('.whiskytable tbody tr');

    $crawler->each(function ($node) {
      //get brands data
      $name = $node->filter('td')->eq(0)->filter('a')->text();
      $country = $node->filter('td')->eq(1)->text();
      if (empty($country)) $country = null;
      $whiskies = $node->filter('td')->eq(2)->text();
      if (empty($whiskies)) $whiskies = null;
      $votes = $node->filter('td')->eq(3)->text();
      if (empty($votes)) $votes = null;
      $rating = $node->filter('td')->eq(4)->text();
      if (empty($rating)) $rating = null;

      //check if finished && new auction
      if (!Bottler::IsExist($name)) {
          //insert to database
          $bottler = new Bottler();
          $bottler->name = $name;
          $bottler->country = $country;
          $bottler->whiskies = $whiskies;
          $bottler->votes = $votes;
          $bottler->rating = $rating;
          $bottler->save();
      }
          
    });
    print_r("\n" . 'Finished saving data.');  
    print_r("\n" . 'Got bottlers. ');  
    print_r("\n" . 'Done!');  
  }

  public static function scrapeDistilleries()
  {   
    $url = env('WHISKY_BASE_URL') . '/whiskies/distilleries';
    print_r("\n" . 'Starting distilleries scraper...');  
    $crawler = Goutte::request('GET', $url);
    print_r("\n" . 'Filtering scraped distilleries data...');  
    $crawler = $crawler->filter('.whiskytable tbody tr');

    $crawler->each(function ($node) {
      //get brands data
      $name = $node->filter('td')->eq(0)->filter('a')->text();
      $country = $node->filter('td')->eq(1)->text();
      if (empty($country)) $country = null;
      $whiskies = $node->filter('td')->eq(2)->text();
      if (empty($whiskies)) $whiskies = null;
      $votes = $node->filter('td')->eq(3)->text();
      if (empty($votes)) $votes = null;
      $rating = $node->filter('td')->eq(4)->text();
      if (empty($rating)) $rating = null;

      //check if finished && new auction
      if (!Distillery::IsExist($name)) {
          //insert to database
          $distillery = new Distillery();
          $distillery->name = $name;
          $distillery->country = $country;
          $distillery->whiskies = $whiskies;
          $distillery->votes = $votes;
          $distillery->rating = $rating;
          $distillery->save();
      }
          
    });
    print_r("\n" . 'Finished saving data.');  
    print_r("\n" . 'Got distilleries. ');  
    print_r("\n" . 'Done!');   
  }

  public static function scrapeProducts1($house)
  {
    print_r('Starting scraper...');        
    $crawler = Goutte::request('GET', $house['site'] . $house['auctions_url']);
    $crawler = $crawler->filter('.productsubcats a.prodbox');
    $crawler->each(function ($node) use ($house) 
    {
      //get auction finished status & title
      $auction_date  =$node->filter('.catinfo .catdate')->text();
      $auction_title =$node->filter('.catinfo .cattitle')->text();
      //check if finished && new auction
      if (strpos($auction_date,'Ended')!==false 
        /*&& !Auction::IsExist($auction_title) && !Auction::IsExistHouseId($house['id'])*/) 
      {
        // auction data
        $auction_date =substr( $auction_date, 9);
        $auction_lots =OtherFunc::get_string_between(
          $node->filter('.catinfo .catproducts')->text(),'are ',' lots') ;
        $auction_url =$node->attr('href');

        // check IsExistAuctionAndHouseId => Insert or get
        $auction_id =static::AuctionExistOperation($house, $auction_title, date('Y-m-d',strtotime($auction_date)), $auction_lots);
        //****************************************************

        //**sub page urls****** scrape: (1~190)
        print_r("\n".'Starting sub pages scraper...');
        $auction_crawler =Goutte::request('GET',$house['site'].'/'.$auction_url);
        $subPages =$auction_crawler->filter('.pages')->eq(0)
                          ->filter('a')->each(function ($node) 
        {
          //get sub page url, 
          return $result[] = $node->attr('href');
        });
        print_r("\n".'Finished sub pages scraper...');
        //*******************************

        //Check if scraped auction?
        $scrapedAuction =static::CheckScrapedAuction($house, $auction_id, $auction_url, $subPages);
        if ($scrapedAuction) return;
        //********************
          

        for ($i=0; $i < count($subPages); $i++) // (1~190)
        {
          //***sub page scraping******** one of (1~190)
          print_r("\n".'Starting sub page scraper...'.$i);  
          $subpageCrawler = Goutte::request('GET', $house['site'].'/'.$subPages[$i]);
          $subpageCrawler->filter('div.productsubcats a')
                          ->each(function ($node) use ($auction_id, $house) 
          { 
            $product_lot_num = substr( $node->filter('.proddesc .prodprice .prodlot')->text(), 12);
            // check if exists, unless create
            if ( is_null( Product::IfExistProduct($product_lot_num, $auction_id) ) )
            {
              // get product title
              $product_title = $node->filter('.proddesc .prodtitle')->text();

              $product_price = null;
              $product_price_text =$node->filter('.proddesc .prodprice .pb .priceline')->text();
              if (OtherFunc::checkIfContainStr($product_price_text,'Winning bid: £'))
              {
                $product_price =StringService::getRestBackPart($product_price_text, 'Winning bid: £');
              }
              $product_detail_url = $node->attr('href');
              // $master_img = $node->filter('.imagespacer')->attr('data-url');
              // if (OtherFunc::is_webfile($master_img))                     
              //   file_put_contents( 
              //     public_path('products/'.basename($master_img)),
              //     @file_get_contents($master_img));
              var_dump($product_title,  $product_price, $product_detail_url);
              //****scrape product view page**** 
              $productCrawler =Goutte::request('GET',$house['site'].'/'.$product_detail_url);
              //*****get product thumbs
              // $product_thumbs =$productCrawler->filter('.productimgthumbs  a')->each(function ($node) 
              // {
              //   $thumb =$node->filter('img')->attr('src');
              //   if (OtherFunc::is_webfile($thumb)) 
              //     file_put_contents( 
              //       public_path('products/'. basename($thumb) ),
              //       @file_get_contents($thumb));
              //   return $result[] = basename($thumb);
              // });
              // var_dump($product_thumbs);
              // get product description
              $product_description= $productCrawler->filter('.productinfo .productdescription')->text();
              $product_description_p =$productCrawler->filter('.productinfo .productdescription p')->each(function ($node) 
              {  
                return $result[] = $node->text();
              });
              // db(age ~ bottle_num) from $proDes
              $product_description_data = StringService::get_from_description($product_description_p);
              // var_dump($product_description_data);
              //*******************************
              //*********************************
              //*****Collecting product data
              $product = array();
              $product['auction_id'] = $auction_id;
              $product['title'] = $product_title;
              $product['url'] = $product_detail_url;
              if (isset($product_description_data['cask_type'])) 
                  $product['cask_type'] = $product_description_data['cask_type'];
              if (isset($product_description_data['cask_num'])) 
                  $product['cask_num'] = $product_description_data['cask_num'];
              $product['description'] = $product_description;
              $product['lot_num'] = $product_lot_num;
              $product['price'] = $product_price;
              // $product['master_img'] = '../products/'.basename($master_img);
              //***********************************
              //*****Insert product data
              print_r("\n".'Insert product <'.$product_title.'> data to DB...');  
              try {
                DB::transaction(function ( ) use ($product/*, $product_thumbs*/){    
                  $product_id = Product::insertGetId($product);
                  // insert data to product_imgs table
                  // for ($j=0; $j < count($product_thumbs) ; $j++) { 
                  //     ProductImg::insert([
                  //         'product_id'=> $product_id,
                  //         'sub_img' => $product_thumbs[$j] 
                  //     ]);
                  // }
                }, 40);
              } catch (\Illuminate\Database\QueryException $ex) {
                print_r("\n".'failed: '.$ex->getMessage()); 
              } catch (Exception $e) {
                  // something went wrong elsewhere, handle gracefully
              }
              // print_r("\n".'Finished inserting to DB...');  
              //************************
            }
          });
          // print_r("\n".'Finished sub page scraper...'.$i);  
          //****************************
        }  
      }
            
    });
    print_r("\n" . 'Got ' . count($crawler) . ' auctions. ');  
    print_r("\n" . 'Done!');   
  }

  public static function scrapeProducts2($house)
  {
    print_r('Starting scraper...');        
    $crawler = Goutte::request('GET', $house['site'] . $house['auctions_url']);
    $crawler = $crawler->filter('.view-content span.field-content');
    $crawler->each(function ($node) use ($house) 
    {
      //get auction finished status & title
      $auction_title =$node->filter('.protitle')->text();
      $end_date = null;
      if (OtherFunc::checkIfContainStr($auction_title,'Auction'))
      {
        $end_date =StringService::getRestPart($auction_title, 'Auction');
        $end_date =OtherFunc::getFirstDateFromStr($end_date);
        if (!$end_date) $end_date = Auction::GetLastEndDate($house['id'])->end_date;
      }
      
      
        // auction data
        $auction_url =$node->filter('a')->attr('href');
        print_r("\n".'auction_title: '.$auction_title);

        // check IsExistAuctionAndHouseId => Insert or get
        $auction_id =static::AuctionExistOperation($house, $auction_title, $end_date, null);
        //************************************************

        //**sub page urls****** scrape: (1~last)
        print_r("\n".'Starting sub pages scraper...');
        $auction_crawler =Goutte::request('GET',$house['site'].'/'.$auction_url);
        $last_page_url =$auction_crawler->filter('.item-list ul.pager')->eq(0)
                          ->filter('.last a');
        if ($last_page_url->count()>0) {
          $last_page_url =$last_page_url->attr('href'); 
          $last_page_num =StringService::getRestBackPart($last_page_url,'&page=');  
          $subpage_prefix =StringService::getRestFrontPart($last_page_url,'&page=');        
        }else{
          $last_page_num ='0'; 
          $subpage_prefix ='/'.$auction_url; 
        }
        // var_dump($last_page_url, $last_page_num, $subpage_prefix);
        print_r("\n".'Finished sub pages scraper...');
        //*******************************

        //Check if scraped auction?
        $scrapedAuction =static::CheckScrapedAuction($house, $auction_id, $auction_url, array($last_page_num, $subpage_prefix));
        if ($scrapedAuction) return;
        //********************

        
        for ($i=0; $i<=(int) $last_page_num; $i++) // (1~last)
        {
          //***sub page scraping******** one of (1~last)
          print_r("\n".'Starting sub page scraper...'.$i);  
          $sub_page_url =$subpage_prefix.'&page='.$i;
          if ($last_page_num==0) $sub_page_url =$subpage_prefix;
          $subpageCrawler = Goutte::request('GET',$house['site'].$sub_page_url)->filter('.block-views');
          $blockIndex = count($subpageCrawler);
          $subpageCrawler->eq($blockIndex-1)->filter('.views-row')
                          ->each(function ($node) use ($auction_id, $house) 
          { 
            $product_lot_num = StringService::getRestBackPart( 
              $node->filter('.views-field .field-content')->eq(0)->filter('a .productfieldhome .lotwin .lotnumber')->text()
              , 'Lot:');
            var_dump('lot:'.$product_lot_num);
            // check if exists, unless create
            if (is_null(Product::IfExistProduct($product_lot_num, $auction_id)))
            {
              // get product title
              $product_title = $node->filter('.views-field .field-content')->eq(0)->filter('a .productfieldhome .protitle')->text();
              $product_price = 0;
              $product_price_text = $node->filter('.views-field .field-content')->eq(0)->filter('a .productfieldhome .lotwin .WinningBid')->text();
              if (OtherFunc::checkIfContainStr($product_price_text,'Winning Bid: £'))
              {
                $product_price_text =StringService::getRestBackPart($product_price_text, '£');
                $product_price =OtherFunc::priceToFloat($product_price_text);
              }
              // db(distillery_brand, name, bottler) from $product_title
              $product_title_data = StringService::get_from_title($product_title);
              $product_detail_url = $node->filter('.views-field .field-content')->eq(0)->filter('a')->attr('href');
              $master_img = $node->filter('.views-field .field-content')->eq(0)->filter('a .productimage img')->attr('src');
              $master_img = StringService::getRestFrontPart($master_img,'?');
              if (OtherFunc::is_webfile($master_img))                     
                file_put_contents( 
                  public_path('products/'.basename($master_img)),
                  @file_get_contents($master_img));
              // var_dump($product_title, $product_title_data, $product_detail_url, 'master_img:'.$master_img);
              // var_dump('detail_url:'.$house['site'].$product_detail_url);
              //****scrape product view page**** 
              $productCrawler =Goutte::request('GET',$house['site'].$product_detail_url);
              //*****get product thumbs
              $product_thumbs =$productCrawler->filter('#new-layout .left .view-whisky-slider')->each(function ($node) 
              {
                $thumb =$node->filter('img')->attr('src');
                $thumb =StringService::getRestFrontPart($thumb,'?');
                if (OtherFunc::is_webfile($thumb)) 
                  file_put_contents( 
                    public_path('products/'. basename($thumb) ),
                    @file_get_contents($thumb));
                return $result[] = basename($thumb);
              });
              // var_dump($product_thumbs);
              //*******************************
              //*********************************
              //*****Collecting product data
              $product = array();
              $product['auction_id'] = $auction_id;
              // $product['url'] = $productUrl;
              $product['title'] = $product_title ;
              $product['price'] = $product_price;
              $product['url'] = $product_detail_url;
              print_r("\n".'Collecting product data...:'.$product_title); 
              $product['name'] = $product_title_data['name'];

              if (isset($product_title_data['series'])) 
                  $product['series'] = $product_title_data['series'];              
              $product['country'] = $product_title_data['country'];
              
              //distillery
              $product_distillery = $productCrawler->filter('.right .topvbn .distillery');
              if ($product_distillery->count()>0) {
                $product['distillery_brand'] =$product_distillery->text();
                if ($product['distillery_brand']=='N/A') $product['distillery_brand'] =null;
              } 

              $product_age = $productCrawler->filter('.right .topvbn .age');
              if ($product_age->count()>0) {
                $product['age'] =(int) $product_age->text();
                if ($product['age']==0) $product['age'] =null;
              }  

              //distilled & region
              $product_distilled = $productCrawler->filter('.right .topvbn .region');
              if ($product_distilled->count()>0) {
                $product['distilled'] =(int) $product_distilled->eq(0)->text();
                if ($product['distilled']=='N/A') $product['distilled'] =null;
                $product['region'] =(int) $product_distilled->eq(1)->text();
                if ($product['region']=='N/A') $product['region'] =null;
              }  

              //bottler & cask_type
              $product_cask_type = $productCrawler->filter('.right .topvbn .casktype');
              if ($product_cask_type->count()>0) {
                $product['bottler'] =$product_cask_type->eq(0)->text();
                if ($product['bottler']=='N/A') $product['bottler'] =null;
                $product['cask_type'] =$product_cask_type->eq(1)->text();
                if ($product['cask_type']=='N/A') $product['cask_type'] =null;
              }    
              
              $product_abv = $productCrawler->filter('.right .topvbn .strength');
              if ($product_abv->count()>0) {
                $product['abv'] = $product_abv->text();
                if ($product['abv']=='N/A') $product['abv'] =null;
              }  
              
              $product_size = $productCrawler->filter('.right .topvbn .bottlesize');
              if ($product_size->count()>0) {
                $product['size'] =$product_size->text();
                if ($product['size']=='N/A') $product['size'] =null;
              }               
              
              $product_topvbn =$productCrawler->filter('.right .topvbn .whiskyproduct div')->each(function ($node) {
                $content =$node->filter('div')->text();
                return $result[] = $content;
              }); 
              $product_description = '';
              $i = 0;
              foreach ($product_topvbn as $topvbn) {
                $i++;
                $product_description .= $topvbn;
                if ($i%2==0) $product_description .= "\n";      
                else $product_description .= " ";
              }

              $product['description'] = $product_description . "\n" .
                $productCrawler->filter('.right .rightvbn .view-content')->text();
              $product['lot_num'] = $product_lot_num;
              $product['master_img'] = '../products/'.basename($master_img);              
              print_r("\n".'Finished collecting product data...:'. $product_title);  
              //***********************************
              //*****Insert product data
              print_r("\n".'Insert product data to DB...');
              try {
                DB::transaction(function ( ) use ($product, $product_thumbs){ 
                  $product_id = Product::insertGetId($product);
                  // insert data to product_imgs table
                  for ($j=0; $j < count($product_thumbs) ; $j++) { 
                      ProductImg::insert([
                          'product_id'=> $product_id,
                          'sub_img' => $product_thumbs[$j] 
                      ]);
                  }
                }, 40);
              } catch (\Illuminate\Database\QueryException $ex) {
                print_r("\n".'failed: '.$ex->getMessage()); 
              } catch (Exception $e) {
                  // something went wrong elsewhere, handle gracefully
              }
              print_r("\n".'Finished inserting to DB...');  
              //************************
            }
          });
          print_r("\n".'Finished sub page scraper...'.$i);  
          //****************************
        }  
         
    });
    print_r("\n" . 'Got auctions. ');  
    print_r("\n" . 'Done!');   
  }

  public static function AuctionExistOperation($house, $auction_title, $end_date, $auction_lots) 
  {
    if ($auction_title=='Strathearn Distillery Inaugural Whisky Auction')
    var_dump($house, $auction_title, $end_date, $auction_lots);
    $check_auction_house = Auction::IsExistAuctionAndHouseId($auction_title, $house['id']);
    if (!$check_auction_house) {          
      //**insert to database
      $auction = new Auction();
      $auction->title =$auction_title;
      $auction->house_id =$house['id'];
      $auction->end_date =$end_date;
      $auction->lots =$auction_lots;
      $auction->save();
      $auction_id =$auction->id;
      //********************
    }else{
      $auction_id =$check_auction_house['id'];
    }
    var_dump($auction_title, $auction_id);
    return $auction_id;
  }

  public static function CheckScrapedAuction($house, $auction_id, $auction_url, $data)
  {
    // var_dump($house, $auction_id, $auction_url, $data);
    //Unless exist auction_id return false
    if (!Product::IfExistAuction($auction_id)) return false;

    //get count transactions from auction_id
    $current =Product::CountProductOfAuction($auction_id);
    //get count transactions from real
    if ($house['site']=='https://www.scotchwhiskyauctions.com/') {
      $lastIndex = count($data) - 1;
      if ($lastIndex == -1) {
        $surfix_url = $auction_url;
        $lastIndex = 0;
      }else
        $surfix_url = $data[$lastIndex];
      $lastpageCrawler = Goutte::request('GET', $house['site'].'/'.$surfix_url)
      ->filter('div.productsubcats a');
    }elseif ($house['site']=='https://www.whiskyauctioneer.com/') {
      $lastIndex = (int) $data[0];
      if ($lastIndex == 0)
        $surfix_url = $data[1];
      else
        $surfix_url = $data[1].'&page='.$lastIndex;
      $lastpageCrawler = Goutte::request('GET',$house['site'].$surfix_url)
        ->filter('.block-views')->eq(1)->filter('.views-row');
    }
    
    $lastpageTransactions = count($lastpageCrawler);
    var_dump($current, $lastIndex, $lastpageTransactions);
    $real = 40 * $lastIndex + $lastpageTransactions;
    var_dump($real);  
    if ($current==$real) return true;
    else return false;
  }

}

