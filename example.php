<?php


require_once './vendor/autoload.php';

$words = require './words.php';

$driver = Crawler\ChromeDriver::getInstance(['--proxy-server=socks5://127.0.0.1:1080']);

try {
    foreach ($words as $word)
    {
        $res = (new Crawler\Google)->searchKeyword($driver, $word['keyword'], $word['url']);
        var_dump($res);
    }
} catch (Exception $e) {
    die($e->getMessage());
}
