<?php

class Lunch
{
    public function __construct($slackKey, $slackChannel = '#general')
    {
        $this->restaurantsFolder = __DIR__ . '/restaurants';

        $this->lunch = array();
        $this->lunches = array();

        $this->slackKey = $slackKey;
        $this->slackChannel = $slackChannel;

        $this->fetchAndParseLunches();
        $this->lunchesToSlack();
    }

    protected function lunchesToSlack()
    {
        $text = "*".$this->today()."*s lunches:".PHP_EOL;

        foreach ($this->lunches as $restaurant => $menu)
        {
            $text .= "*".$restaurant."* ".$menu.PHP_EOL;
        }

        $postFields = array(
            'token' => $this->slackKey,
            'icon_url' => 'http://i.imgur.com/PWxRcm1.png',
            'channel' => $this->slackChannel,
            'username' => 'lunch',
            'text' => $text,
            'parse' => 'full',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://slack.com/api/chat.postMessage');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        $result = curl_exec($ch);
        curl_close($ch);

        return true;
    }

    protected function today()
    {
        return $this->weekNumTotext(date("N", time()) - 1);
    }

    protected function weekNumToText($weekNum)
    {
        switch ($weekNum) {
            case 0: return 'Monday'; break;
            case 1: return 'Tuesday'; break;
            case 2: return 'Wednesday'; break;
            case 3: return 'Thursday'; break;
            case 4: return 'Friday'; break;
        }
    }

    protected function fixSpaces($string)
    {
        return preg_replace('/[ ]{2,}/', ' ', $string);
    }

    protected function cleanStr($string)
    {
        $strArr = str_split($string);
        $cleanStr = '';
        foreach ($strArr as $asciiChar) {
            $charNum = ord($asciiChar);
            if ($charNum > 31 && $charNum < 127 || $charNum == 10 || $charNum == 163) {
                $cleanStr .= $asciiChar;
            }
        }
        return $cleanStr;
    }

    private function curlRequest($url, $postData = array(), $refererUrl = null, $gzippedPage = FALSE)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_VERBOSE, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        if (null !== $refererUrl) {
            curl_setopt($ch, CURLOPT_REFERER, $refererUrl);
        }

        $httpHeader = array(
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/' . '*;q=0.8',
            'Accept-Encoding: gzip, deflate',
            'Accept-Language: en-US,en;q=0.8',
            'Connection: keep-alive',
            'Cache-Control: max-age=0',
            'HTTPS: 1',
            'Cache-Control: no-cache, no-store',
            'Pragma: no-cache',
        );

        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);

        $retrievedPage = curl_exec($ch);

        if ($gzippedPage) {
            if (function_exists('gzdecode')) {
                $retrievedPage = gzdecode($retrievedPage);
            } else {
                $retrievedPage = gzinflate(substr($retrievedPage, 10, -8));
            }
        }

        return array(
            'curl_info' => curl_getinfo($ch),
            'contents' => $retrievedPage
        );
    }

    private function fetchAndParseLunches()
    {
        if (!is_dir($this->restaurantsFolder)) {

            mkdir($this->restaurantsFolder);
            return array();

        } else {

            $rClasses = array_diff(
                scandir($this->restaurantsFolder),
                array(
                    '..',
                    '.'
                )
            );

            foreach ($rClasses as $rClass) {
                if ($rClass != '.' && $rClass != '..') {
                    try {
                        $className = preg_replace('/\.php$/', '', $rClass);

                        require_once($this->restaurantsFolder.'/'.$rClass);

                        $x = new $className;
                        if (true === $x->enabled) {
                            $request = $this->curlRequest($x->url, $x->postData, $x->refererUrl, $x->gzipped);

                            $reqArray = array(
                                'restaurant' => $className,
                                'lunchList' => $x->HTMLtoLunchArray($request['contents']),
                            );

                            $lunchArray[] = $reqArray;

                            $this->lunches[$className] = $reqArray['lunchList'][$this->today()];
                        } else {
                            echo 'disabled: '.$className.PHP_EOL;
                        }
                    } catch (Exception $e) {
                        var_dump($e);
                    }
                }
            }
        }
    }
}
