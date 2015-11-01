<?php

require __DIR__ . 'slack-api/Slack.php';

class Lunch
{
    public function __construct($slackKey, $slackChannel)
    {
        $this->restaurantsFolder = __DIR__ . '/restaurants';
        $this->lunch = array();
        $this->slackKey = $slackKey;
        $this->slackChannel = $slackChannel;
        $this->lunches = array();
        $this->fetchAndParseLunches();
        $this->lunchesToSlack($this->lunches);
    }

    protected function lunchesToSlack($lunchArray)
    {
        $text = "Lunches for *".$this->today()."*".PHP_EOL;
        foreach ($lunchArray as $restaurant => $menu)
        {
            $text .= "*".$restaurant."* ".$menu.PHP_EOL;
        }

        $Slack = new Slack($this->slackKey);

        $Slack->call('chat.postMessage', array(
            'icon_url' => 'http://i.imgur.com/PWxRcm1.png',
            'channel' => $this->slackChannel,
            'username' => 'lunch',
            'text' => $text,
            'parse' => 'full',
        ));
    }

    protected function today()
    {
        return $this->weekNumTotext(date("N", time()));
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

    protected function cleanStr($string) {
        $strArr = str_split($string);
        $cleanStr = '';
        foreach ($strArr as $aChar) {
            $charNo = ord($aChar);
            if ($charNo > 31 && $charNo < 127 || $charNo == 10 || $charNo == 163) {
                $cleanStr .= $aChar;
            }
        }
        return $cleanStr;
    }

    private function curlRequest($url, $post_data = array(), $referer = null, $page_is_gzipped = FALSE)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_VERBOSE, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        if (null !== $referer) {
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        }

        $http_header = array(
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/' . '*;q=0.8',
            'Accept-Encoding: gzip, deflate',
            'Accept-Language: en-US,en;q=0.8',
            'Connection: keep-alive',
            'Cache-Control: max-age=0',
            'HTTPS: 1',
            'Cache-Control: no-cache, no-store',
            'Pragma: no-cache',
        );

        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);

        $retrieved_page = curl_exec($ch);

        if ($page_is_gzipped) {
            if (function_exists('gzdecode')) {
                $retrieved_page = gzdecode($retrieved_page);
            } else {
                $retrieved_page = gzinflate(substr($retrieved_page, 10, -8));
            }
        }

        $curl_info = curl_getinfo($ch);

        return array(
            'curl_info' => $curl_info,
            'contents' => $retrieved_page
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
                try {
                    if ($rClass != '.' && $rClass != '..') {

                        $className = preg_replace('/\.php$/', '', $rClass);

                        require_once($this->restaurantsFolder.'/'.$rClass);

                        $x = new $className;
                        if (true === $x->enabled) {
                            $request = $this->curlRequest($x->url, $x->postData, $x->referer, $x->gzipped);

                            $reqArray = array(
                                'restaurant' => $className,
                                'lunchList' => $x->HTMLtoLunchArray($request['contents']),
                            );

                            $lunchArray[] = $reqArray;

                            $this->lunches[$className] = $reqArray['lunchList'][$this->today()];
                        } else {
                            echo 'disabled: '.$className.PHP_EOL;
                        }
                    }
                } catch (Exception $e) {
                    var_dump($e);
                }
            }
        }
    }
}
