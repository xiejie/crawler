<?php
use PHPUnit\Framework\TestCase;

class KeywordTest extends TestCase
{
    public function testSearch()
    {
        $driver = Crawler\ChromeDriver::getInstance(['--proxy-server=socks5://127.0.0.1:1080']);
        $word = [
            'url' => 'www.google.com',
            'keyword' => 'google'
        ];

        (new Crawler\Google)->searchKeyword($driver, $word['keyword'], $word['url']);

//         $this->assertTrue(true);
    }

}
?>