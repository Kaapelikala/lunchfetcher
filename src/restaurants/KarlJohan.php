<?php

class KarlJohan extends Lunch
{
    protected $url = "http://www.ravintolakarljohan.fi/lounas/";
    protected $postData = array();
    protected $referer = "";
    protected $gzipped = false;
    protected $enabled = true;

    public function __construct()
    {
    }

    protected function HTMLtoLunchArray($html)
    {
        $doc = new DOMDocument();
        @$doc->loadHTML($html);
        $xpath = new DOMXpath($doc);

        $dElements = array(
            0 => array(2, 3, 4),
        );

        $arr = array();

        foreach ($dElements as $dId => $dArr) {
            $dayMenu = array();
            foreach ($dArr as $dRow) {
                $element = $xpath->query(
                    '/html/body/div[2]/div/div/div/div/div['.$dRow.']/div[1]'
                );

                foreach ($element as $e) {
                    $dayMenu[] = trim($e->nodeValue);
                }
            }
            $weekDay = parent::today();
            $arr[$weekDay] = trim(utf8_decode(implode(' / ', $dayMenu)));
        }

        if (!empty($arr)) {
            return $arr;
        }

        return false;
    }
}
