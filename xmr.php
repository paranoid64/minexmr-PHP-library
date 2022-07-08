<?php

#https://minexmr.com/apidoc

require_once ('config.php');
require_once ('minexmr.class.php');

echo '<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Monero XMR</title>
	<meta charset="utf-8">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <meta http-equiv="refresh" content="'.$refresh.'">
	<link rel="stylesheet" type="text/css" href="css/xmr.css">
</head>
<body>
';

#first step, build cachefiles (local json)
$monero = new monero();
$monero->init();

echo '<div id="news">
    <marquee direction="left" behavior="scroll" scrollamount="4" scrolldelay="1" onmouseover="this.stop();" onmouseout="this.start();">

        + + +
        <b>XMR exchange rate:</b>
        '.$monero->format_currency($monero->currency_exchange_rate("eur"),2).' EUR &nbsp;&nbsp;|&nbsp;&nbsp;
        '.$monero->format_currency($monero->currency_exchange_rate("rub"),2).' RUB &nbsp;&nbsp;|&nbsp;&nbsp;
        '.$monero->format_currency($monero->currency_exchange_rate("gbp"),2).' GBP &nbsp;&nbsp;|&nbsp;&nbsp;
        '.$monero->format_currency($monero->currency_exchange_rate("usd"),2).' USD &nbsp;&nbsp;|&nbsp;&nbsp;
        '.$monero->format_currency($monero->currency_exchange_rate("btc"),2).' BTC &nbsp;&nbsp;

        + + +
        <b>Pool Hashrate:</b> '.$monero->pool('hashrate').' &nbsp;&nbsp;|&nbsp;&nbsp;
        <b>Active Miners:</b> '.$monero->pool('activeMiners').' &nbsp;&nbsp;|&nbsp;&nbsp;
        <b>Last Block Found:</b> '.$monero->pool('lastBlockFound').'&nbsp;&nbsp;|&nbsp;&nbsp;
        <b>Total Blocks:</b> '.$monero->pool('totalBlocks').' &nbsp;&nbsp;|&nbsp;&nbsp;
        <b>Blocks per Day:</b> '.$monero->pool('blocksDay').' &nbsp;&nbsp;|&nbsp;&nbsp;
        <b>Calc PPS:</b> '.$monero->pool('calcPPS').' &nbsp;&nbsp;|&nbsp;&nbsp;

        + + +
        <b>Difficulty:</b> '.$monero->network('difficulty').' &nbsp;&nbsp;|&nbsp;&nbsp;
        <b>Height:</b> '.$monero->network('height').' &nbsp;&nbsp;|&nbsp;&nbsp;
        <b>Timestamp:</b> '.$monero->network('timestamp').' &nbsp;&nbsp;|&nbsp;&nbsp;
        <b>Reward:</b> '.$monero->network('reward').' &nbsp;&nbsp;|&nbsp;&nbsp;
        <b>Hash:</b> '.$monero->network('hash').' &nbsp;&nbsp;&nbsp;&nbsp;
         + + +

    </marquee>
</div>
';


$EUR = $monero->xmr() * $monero->currency_exchange_rate("eur");
$RUB = $monero->xmr() * $monero->currency_exchange_rate("rub");
$GBP = $monero->xmr() * $monero->currency_exchange_rate("gbp");
$USD = $monero->xmr() * $monero->currency_exchange_rate("usd");
$BTC = $monero->xmr() * $monero->currency_exchange_rate("btc");

    echo '  <div class="content">
        <h1>Miner balance stats</h1>

        <b>Total Rewards: </b>'.$monero->format_currency($monero->xmr(),6).'<br>
        <b>Paid: </b>'.$monero->paid().' XMR<br><br>
        <b>In EUR: </b>'.$monero->format_currency($EUR,2).' EUR<br>
        <b>In RUB: </b>'.$monero->format_currency($RUB,2).' RUB<br>
        <b>In GBP: </b>'.$monero->format_currency($GBP,2).' GBP<br>
        <b>In USD: </b>'.$monero->format_currency($USD,2).' USD<br>
        <b>In BTC: </b>'.$monero->format_currency($BTC,2).' BTC<br>

        <h1>Information about the '.$monero->count_worker().' workers </h1>
';
    echo $monero->get_worker();

    echo '</div>

    <div id="footer">
            this class is open source and may not be offered for sale. <a href="https://www.lama-creation.de" target="blank">LaMa-Creation</a>
        </div>';

echo '</body>
</html>';
?>
