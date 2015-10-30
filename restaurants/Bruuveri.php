<?php

class Bruuveri extends Lunch
{
    protected $url = "http://bruuveri.fi/lounas/";
    protected $postData = array();
    protected $referer = "";
    protected $gzipped = true;
    protected $enabled = true;

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

        $lunchListInHTML = utf8_encode($doc->saveHTML($element->item(0)));
        $lunchListInArray = explode('<hr class="mini">', $lunchListInHTML);

        foreach ($lunchListInArray as $lunchPerDay) {
            $lunchPerDay = $this->cleanStr($lunchPerDay);

            $foods = preg_replace('/^[a-zA-Z]{2} [0-9]{1,2}\.[0-9]{1,2}\./', '', trim(strip_tags($lunchPerDay)));
            $foodsArray = explode(PHP_EOL, $foods);

            $dayMenu = array();
            foreach ($foodsArray as $food) {
                $dayMenu[] = $food;
            }

            $weekDay = parent::weekNumToText($i);
            $arr[$weekDay] = implode(' / ', $dayMenu);
            $i++;
        }

        if (!empty($arr)) {
            return $arr;
        }

        return false;
    }
}