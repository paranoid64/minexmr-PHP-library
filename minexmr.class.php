<?php

/*
*    API Documentation
*    https://minexmr.com/apidoc
*/

require_once ('config.php');

class monero
{

    private $cache_pool = "./cache/Miner_pool.json";
    private $cache_stats = "./cache/Miner_stats.json";
    private $cache_worker = "./cache/Miner_worker.json";

    private $data_pool;
    private $data_stats;
    private $data_worker;

    /*
    *    creates cache files
    *    Currently, one minute is the maximum cache age.
    */

    private function cache($cache_file, $url){
	    if (file_exists($cache_file) && (filemtime($cache_file) > (time() - 60 ))) {
	       $file = file_get_contents($cache_file);
	    } else {
	       //  cache is out-of-date
	       $file = file_get_contents($url);
	       file_put_contents($cache_file, $file, LOCK_EX);
	    }
	    $json = file_get_contents($cache_file);
	    return json_decode($json);
    }


    /*
    * this is needed to make all data locally available.
    */

    public function init(){
        global $user;

        //Cache folder
        if (!file_exists('./cache')) {
            mkdir('./cache', 0755, true);
        }

        $url = "https://minexmr.com/api/main/pool/stats";
        $this->data_pool = $this->cache($this->cache_pool, $url);

        $url="https://minexmr.com/api/main/user/stats?address=" . $user;
        $this->data_stats = $this->cache($this->cache_stats, $url);

        $url="https://minexmr.com/api/main/user/workers?address=" . $user;
        $this->data_worker = $this->cache($this->cache_worker, $url);
    }


    /**
    * returns urrency exchange rate
    *
    * @param unit (eur, rub, gbp, usd, btc)
    * @return e.g. eur  113,50
    */
    public function currency_exchange_rate($unit){

        foreach($this->data_pool as $pool){
	        if (isset($pool->monero)){

                switch($unit) {
                  case ("eur"):
                    $ret = $pool->monero->eur;
                    break;
                  case ("rub"):
                    $ret = $pool->monero->rub;
                    break;
                  case ("gbp"):
                    $ret = $pool->monero->gbp;
                    break;
                  case ("usd"):
                    $ret = $pool->monero->usd;
                    break;
                  case ("btc"):
                    $ret = $pool->monero->btc;
                    break;
                }
	        }
        }
        return $ret;
    }


    //"network":{"difficulty":328346974047,"height":2658604,"timestamp":1656775531,"reward":600858300000,"hash":"79fb31f17727e0414fe6d4cda8bcb4b8f1817acd68fbd7de0e138b1902f772b8"}

    /**
    * returns pool network information
    *
    *difficulty	->	Current network difficulty
    *height      ->	Current network height
    *timestamp	->	Timestamp from latest network block
    *reward	    ->  Block reward for latest network block
    *hash	    ->  Hash of latest network block

    *
    * @param (difficulty, height, timestamp, reward, hash)
    * @return the requested val from param.
    */
    public function network($val){

        foreach($this->data_pool as $pool){
            if (isset($pool->difficulty)){
                switch($val) {
                  case ('difficulty'):
                        return $pool->difficulty;
                    break;
                  case ("timestamp"):
                        return $this->format_date($pool->timestamp); // this timestamp is in milliseconds. Therefore this must be divided by 1000.
                    break;
                  case ("height"):
                        return $pool->height;
                    break;
                  case ("reward"):
                        return $this->format_currency($pool->reward,0);
                    break;
                  case ("hash"):
                        return $pool->hash;
                    break;
                }
            }
        }

    }


    /**
    * returns pool information
    *
    *    hashrate	->	Current pool hashrate
    *    lastBlockFound	->	Timestamp of last block found by the pool
    *    activeMiners	->	Current number of pool miners
    *    totalBlocks	->	Number of blocks found by the pool
    *    blocksDay	->	Number of blocks found by the pool in last 24h
    *    calcPPS	->	Estimated profit per hash in last 24h
    *
    * @param (hashrate, lastBlockFound, activeMiners, totalBlocks, blocksDay, calcPPS)
    * @return the requested val from param.
    */
    public function pool($val){

        foreach($this->data_pool as $pool){
            if (isset($pool->hashrate)){
                switch($val) {
                  case ('hashrate'):
                        return $this->format_currency($pool->hashrate,0);
                    break;
                  case ("lastBlockFound"):
                        return $this->format_date($pool->lastBlockFound / 1000); // this timestamp is in milliseconds. Therefore this must be divided by 1000.
                    break;
                  case ("activeMiners"):
                        return $pool->activeMiners;
                    break;
                  case ("totalBlocks"):
                        return $pool->totalBlocks;
                    break;
                  case ("blocksDay"):
                        return $pool->blocksDay;
                    break;
                  case ("calcPPS"):
                        return $pool->calcPPS;
                    break;
                }
            }
        }

    }


    /*
    * return Current balance
    */

    public function balance(){
        return $this->data_stats->balance;
    }

    /*
    * return Auto payout threshold
    */
    public function thold(){
        return $this->data_stats->thold;
    }

    /*
    * return Total amount paid
    */
    public function paid(){
        $paid = ($this->data_stats->paid / $this->thold()) / 10;
        $paid = number_format(
                                $paid,
						        6,
						        ",",// Dezimaltrennzeichen
						        "." // 1000er-Trennzeichen
					        );
        return $paid;
    }

    /* your xmr */
    public function xmr(){
        return ($this->balance() / $this->thold()) / 10;
    }

    /*
    * Your xmr in current rate of the currency
    * @param $unit (eur, rub, gbp, usd or btc)
    * @return ( 0,010351 XMR * 124,92 EUR = 1,29 EUR)
    */

    public function xmr_current_rate($unit){
         switch($unit) {
          case ("eur"):
            $ret = $this->xmr() * $this->currency_exchange_rate("eur");
            break;
          case ("rub"):
            $ret = $this->xmr() * $this->currency_exchange_rate("rub");
            break;
          case ("gbp"):
            $ret = $this->xmr() * $this->currency_exchange_rate("gbp");
            break;
          case ("usd"):
            $ret = $this->xmr() * $this->currency_exchange_rate("usd");
            break;
          case ("btc"):
            $ret = $this->xmr() * $this->currency_exchange_rate("btc");
            break;
        }

        return $this->format_currency($ret,2);
    }


    /*
        return: number of workers
    */

    public function count_worker(){
        return count($this->data_worker);
    }


    /*
    *    return a json with details of the workers,
    *    if you want to design the output yourself.
    */
    public function get_worker_json(){
        return $this->data_worker;
    }

    /*
    *    return simple HTML string with details of the workers.
    */
    public function get_worker(){
        $worker_id = 1;
        $ret = '<div class="workers">';

        foreach($this->data_worker as $worker){

            $ret .= '<div class="worker">';

	        if (isset($worker->name)){
		        $ret .= "<h2>" . $worker->name . "</h2>";
	        } else {
		        $ret .= "<h2>Worker ID " . $worker_id . "</h2>";
	        }

	        $ret .= "<b>hashrate:</b> " . $this->format_currency($worker->hashrate, 0) . " h/s<br>";
	        $ret .= "<b>hashes:</b> " . $this->format_currency($worker->hashes, 0) . " Total accepted<br>";

	        if (isset($worker->expired)){
		        $ret .= "<b>expired:</b> " . $this->format_currency($worker->expired, 0) . " Total expired shares<br>";
	        }

	        if (isset($worker->invalid)){
		        $ret .=  "<b>invalid:</b> " . $this->format_currency($worker->invalid, 0) . " Total invalid shares<br>";
	        }

	        $date1 = strtotime("now");
	        $date2 = $worker->lastShare;
	        $interval = $date1 - $date2;

	        if ((int)$interval>500){
		        $status="offline";
	        } else {
		        $status="online";
	        }

	        $ret .=  "<b class=\"".$status."\">lastShare:</b> " . $this->format_date($worker->lastShare) . "<br>";
	        $worker_id++;
	        $ret .=  "</div>";
        }

        $ret .= '</div>';

        return $ret;
    }


    public function format_date($val){

        global $format;

        switch($format) {
          case ("de"):
            return date("d.m.Y - H:i", $val);
            break;
          case ("en"):
            return date("d-m-Y - H:i", $val);
            break;
          case ("us"):
            return date("Y/m/d - H:i", $val);
            break;
        }


    }

    public function format_currency($val, $dec){

        global $format;

        switch($format) {
          case ("de"):
            return number_format(
                            $val,
                            $dec,
                            ",",// Decimal separator
                            "." // 1000-separator
                        );
            break;
          case ("en"):
            return number_format(
                            $val,
                            $dec,
                            ".",// Decimal separator
                            "," // 1000-separator
                        );
            break;
          case ("us"):
            return number_format(
                            $val,
                            $dec,
                            ".",// Decimal separator
                            "," // 1000-separator
                        );
            break;
        }
    }

}

?>
