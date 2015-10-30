<?php

class Bruuveri extends Lunch
{
//    protected $url = "http://bruuveri.fi/lounas/";
    protected $url = "http://topkekeke.com/bruuveri.html";
    protected $postData = array();
    protected $referer = "";
    protected $gzipped = true;
    protected $enabled = true;

    protected function HTMLtoLunchArray($html)
    {
        $doc = new DOMDocument();
        @$doc->loadHTML($html);
        $xpath = new DOMXpath($doc);

        $dElements = array(
            0 => array(2, 3, 4),
        );

        $arr = array();

        $element = $xpath->query(
            '//*[@id="menu"]/div[*]'
        );

        $arr = array();
        $i = 0;

        $lunchListInHTML = utf8_encode($doc->saveHTML($element->item(0)));
        $lunchListInArray = explode('<hr class="mini">', $lunchListInHTML);
        foreach ($lunchListInArray as $lunchPerDay) {
            var_dump(hex2bin(bin2hex($lunchPerDay)));
            $foods = preg_replace('/^[a-zA-Z]{2} [0-9]{1,2}\.[0-9]{1,2}\./', '', trim(strip_tags($lunchPerDay)));
            $foodsArray = explode("\n", $foods);

//            var_dump($foods);

//            var_dump($foodsArray);

            $dayMenu = array();
            foreach ($foodsArray as $food) {
                $dayMenu[] = $food;
            }
            $arr[$i] = implode(' / ', $dayMenu);
            $i++;
        }

        if (!empty($arr)) {
            return $arr;
        }

        return false;
    }
}