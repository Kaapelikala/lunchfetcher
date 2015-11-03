<?php

class Harald extends Lunch
{
    protected $url = "http://www.ravintolaharald.fi/ruoka--ja-juomalistat/lounas";
    protected $postData = array();
    protected $refererUrl = null;
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
            1 => array(8, 9, 10),
            2 => array(14, 15, 16),
            3 => array(20, 21, 22),
            4 => array(26, 27, 28),
        );

        $arr = array();
        $i = 0;

        foreach ($dElements as $dId => $dArr) {
            $dayMenu = array();
            foreach ($dArr as $dRow) {
                $element = $xpath->query(
                    '//*[@id="lounaslistaTable"]/tr['.$dRow.']/td[1]'
                );

                foreach ($element as $e) {
                    $dayMenu[] = trim($e->nodeValue);
                }
            }
            $weekDay = parent::weekNumToText($i);
            $arr[$weekDay] = parent::fixSpaces(trim(implode(' / ', $dayMenu)));
            $i++;
        }

        if (!empty($arr)) {
            return $arr;
        }

        return false;
    }
}
