# minexmr-PHP-library
This PHP class uses the minexmr API to simplify displaying the data on a own website.
for more details see https://minexmr.com/apidoc

## Instruction

1.  copies the files into the web directory.

2.  open the file config.php

3.  Add you YOUR WALLET ADDRESS in $user = ""

4.  The script needs privileges, because a cache folder is created. There are the json as cache.
    API access is limited to 100 requests / 15 minutes / IP. Responses are cached for 1 minute so do not make more frequent requests
    Monero amounts are given in piconero (0.000000000001 XMR).

5. Open the url: your-domain.tld/folder/xmr.php


## Description

### Files

1.  minexmr.class.php is the actual class, nothing should be changed in the file.

2.  config.php is also required for the class.

    $user is your WALLET ADDRESS

    $format is to change the output format
        de = 123,00 EUR , 07.12.2022 - 12:30
        en = 123.00 EUR , 07-12-2022 - 12:30
        us = 123.00 EUR , 12/07/2022 - 12:30

    $refresh is for page refresh time in seconds. 
    This ist only for the meta tag, this is not required for the class.

3.  all other files, serve only as example script. The layout/css is also responsive, so that it also works on mobile devices.


### CLASS

#### initialize the class and create cache:

    this must be done first, only then can the functions be called.

    $monero = new monero();
    $monero->init();


#### Get Pool informationen:

    *    hashrate	->	Current pool hashrate
    *    lastBlockFound	->	Timestamp of last block found by the pool
    *    activeMiners	->	Current number of pool miners
    *    totalBlocks	->	Number of blocks found by the pool
    *    blocksDay	->	Number of blocks found by the pool in last 24h
    *    calcPPS	->	Estimated profit per hash in last 24h

    For example, to output the current pool hashrate:
    $monero->pool('hashrate')

#### Get Pool-Network informationen:

    *difficulty	->	Current network difficulty
    *height      ->	Current network height
    *timestamp	->	Timestamp from latest network block
    *reward	    ->  Block reward for latest network block
    *hash	    ->  Hash of latest network block

    For example, to output the current network difficulty

    $monero->network('difficulty')

#### Convert number to currency format

    2 parameters are needed, one is the number to format and parameter 2 is to tell how many decimal places to display.

    For example, to output the currency exchange rate:

    $monero->format_currency($monero->currency_exchange_rate("eur"),2)

    The output is now dependent on the config.php setting.
    * de = 123,00 EUR
    * en = 123.00 EUR
    * us = 123.00 EUR

#### format date
     
    requires a date in timestamp to be formatted

    example:
    $monero->format_date('1657313373800')
    
    The output is now dependent on the config.php setting.
    * de = 07.12.2022 - 12:30
    * en = 07-12-2022 - 12:30
    * us = 12/07/2022 - 12:30

#### Currency exchange rate

    Output eur, rub, gbp, usd or btc exchange rate.

    example:
    $monero->currency_exchange_rate("rub");

    Return: 8.259,91 RUB

#### Total amount paid

    example:
    $monero->paid()

#### your xmr

    example:
    $monero->xmr()



## more information will follow, the manual is still under construction.