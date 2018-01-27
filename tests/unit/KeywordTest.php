<?php
use PHPUnit\Framework\TestCase;

class KeywordTest extends TestCase
{
    public function testSearch()
    {
        $driver = Crawler\ChromeDriver::getInstance(['--proxy-server=socks5://127.0.0.1:1080']);
        $data = (new Crawler\Google)->searchKeyword($driver, 'google', 'www.google.com');

        $this->assertEquals(
                ['keyword' => 'google' , 'page' => 1, 'rank' => 1],
                $data
            );
    }

}
?>