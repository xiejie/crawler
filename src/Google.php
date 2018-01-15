<?php

namespace Crawler;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Symfony\Component\DomCrawler\Crawler;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Google
{
    /**
     * 获取关键词排名
     *
     * @param RemoteWebDriver $driver
     * @param string $keyword
     * @param string $url
     * @param number $page
     * @param number $pause
     * @return number[]|unknown[]|boolean[]|NULL
     */
    public function searchKeyword(RemoteWebDriver $driver, $keyword, $url, $page = 3, $pause = 15)
    {
        // 当脚本执行完成或者 exit() 后被调用
        register_shutdown_function(function () use ($driver) {
            $driver->quit();
        });

        if ($driver->getCurrentURL() === 'data:,') {
            $driver->get('https://www.google.com/ncr');
        }

        $element = $driver->findElement(WebDriverBy::xpath('//input[@name="q"]'));
        $driver->wait()->until(WebDriverExpectedCondition::visibilityOf($element));

        $element->clear()->sendKeys($keyword);
        $element->sendKeys(WebDriverKeys::ENTER);

        // 查几页
        for ($i = 0; $i < $page; $i ++) {
            // 间隔一段时间，防止google 发现
            usleep(mt_rand(($pause - 1) * 1000000, ($pause + 1) * 1000000));

            $searchResult = $this->processHtml($driver->getPageSource());
            if ($rank = $this->getRank($searchResult, $url)) {
                return [
                    'keyword' => $keyword,
                    'page' => $i+1,
                    'rank' => $rank
                ];
            }

            if ($i < $page - 1) {
                try {
                    $element = $driver->findElement(WebDriverBy::xpath('//a[span="Next"]'));
                    $driver->wait(10)->until(WebDriverExpectedCondition::visibilityOf($element));
                    $element->click();
                } catch (\Exception $e) {
                    // no such element: Unable to locate element: {"method":"xpath","selector":"//a[span="Next"]"}
                }
            }
        }
        return null;
    }

    /**
     * 处理html
     *
     * @param string $html
     * @return array
     */
    private function processHtml($html)
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent($html, 'utf-8');

        return $crawler->filterXPath('//div[@id="search"]//div[@class="g"]')->each(function (Crawler $node, $i) {
            $url = $node->filter('cite')->count() ?
                        $node->filter('cite')->text() :
                        ($node->filterXPath('//h3//a')->count() ? 'https://www.google.com' . $node->filterXPath('//h3//a')->eq(0)->attr('href') : '');

            return [
                'title' => $node->filter('h3 a')->count() ? $node->filter('h3 a')->eq(0)->text() : '',
                'url' => $url,
                'rank' => ++$i
            ];
        });
    }
    
    /**
     * 获取结果中排名
     *
     * @param array $data
     * @param string $url
     * @return number|boolean
     */
    private function getRank($data, $url)
    {
        $host = parse_url($url, PHP_URL_HOST) ?: $url;
        foreach ($data as $item)
        {
            if ($host == parse_url($item['url'], PHP_URL_HOST) || strpos($item['url'], $host)  !== false)
            {
                return $item['rank'] + 1;
            }
        }
        return false;
    }
}