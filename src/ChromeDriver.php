<?php

namespace Spider;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;

class ChromeDriver
{

    private static $driver;
    
    public static function getInstance()
    {
        if (is_null(static::$driver)) {
            $host = 'http://localhost:4444/wd/hub';

            $capabilities = DesiredCapabilities::chrome();

            // chromedriver headless
            $options = new ChromeOptions();
            $options->addArguments ( array (
                '--disable-gpu',
                '--headless',
                '--no-sandbox',
                '--user-agent=Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.79 Safari/537.36'
            ) );
//             $options->addArguments(array('--proxy-server=socks5://127.0.0.1:1080'));
            $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
            
            static::$driver = RemoteWebDriver::create($host, $capabilities, 5000, 5000);
        }

        return static::$driver;
    }
}