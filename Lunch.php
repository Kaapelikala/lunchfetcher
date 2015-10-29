<?php

class Lunch
{
    public function __run()
    {
        $this->restaurantsFolder = __DIR__ . '/restaurants';
        $this->settingsFile = __DIR__ . '/settings.json';
        $this->dateN = date("N", time());

        $this->listRestaurants();
    }

    protected function weekNumToText($weekNum)
    {
        switch ($weekNum) {
            case 0: return 'Monday'; break;
            case 1: return 'Tuesday'; break;
            case 2: return 'Wednesday'; break;
            case 3: return 'Thursday'; break;
            case 4: return 'Friday'; break;
            default: return 'dunnolol';
        }
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

    private function listRestaurants()
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
                        $request = $this->curlRequest($x->url, $x->postData, $x->referer, $x->gzipped);
                        $lunchArray = array(
                            'restaurant' => $className,
                            'lunchList' => $x->HTMLtoLunchArray($request['contents']),
                        );

                        var_dump($lunchArray);
                    }
                } catch (Exception $e) {
                    var_dump($e);
                }
            }
        }
    }
}

$l = new Lunch();
$l->__run();