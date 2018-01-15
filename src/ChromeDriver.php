<?php

namespace Spider;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;

class ChromeDriver
{

    private static $driver;

    /**
     * @param array $option
     * @param number $connection_timeout_in_ms
     * @param number $request_timeout_in_ms
     * @return RemoteWebDriver
     */
    public static function getInstance($option = null, $connection_timeout_in_ms = 10000, $request_timeout_in_ms = 10000)
    {
        if (is_null(static::$driver)) {
            $host = 'http://localhost:4444/wd/hub';

            $capabilities = DesiredCapabilities::chrome();

            // chromedriver
            $options = new ChromeOptions();
            $options->addArguments ( array (
                '--disable-gpu',
                '--no-sandbox',
                '--user-agent=Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.79 Safari/537.36'
            ) );
            
            if (!is_null($option)) {
                // array('--headless', '--proxy-server=socks5://127.0.0.1:1080')
                $options->addArguments($option);
            }

            $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

            static::$driver = RemoteWebDriver::create($host, $capabilities, $connection_timeout_in_ms, $request_timeout_in_ms);
        }

        return static::$driver;
    }

}