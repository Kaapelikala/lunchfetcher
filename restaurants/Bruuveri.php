<?php

class Bruuveri extends Lunch
{
    protected $url = "http://bruuveri.fi/lounas/";
    protected $postData = array();
    protected $refererUrl = null;
    protected $gzipped = true;
    protected $enabled = true;

    public function __construct()
    {
    }

    protected function HTMLtoLunchArray($html)
    {
        $doc = new DOMDocument();
        @$doc->loadHTML($html);
        $xpath = new DOMXpath($doc);

        $element = $xpath->query(
            '//*[@id="menu"]/div[*]'
        );

        $arr = array();
        $i = 0;

        $lunchListInHTML = $doc->saveHTML($element->item(0));
        $lunchListInArray = explode('<hr class="mini">', $lunchListInHTML);

        foreach ($lunchListInArray as $lunchPerDay) {
            $foods = preg_replace('/^[a-zA-Z]{2} [0-9]{1,2}\.[0-9]{1,2}\./', '', trim(strip_tags($lunchPerDay)));
            $foodsArray = explode(PHP_EOL, $foods);

            $dayMenu = array();
            foreach ($foodsArray as $food) {
                $dayMenu[] = preg_replace('/ ([a-zA-Z,]+)$/', ' (${1})', parent::cleanStr($food));
            }

            $weekDay = parent::weekNumToText($i);
            $arr[$weekDay] = parent::fixSpaces(implode(' / ', $dayMenu));
            $i++;
        }

        if (!empty($arr)) {
            return $arr;
        }

        return false;
    }
}
