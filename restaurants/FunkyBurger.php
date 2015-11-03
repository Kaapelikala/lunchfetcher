<?php

class FunkyBurger extends Lunch
{
    protected $url = "http://funkyburger.net/";
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
            0 => array(1),
            1 => array(2),
            2 => array(3),
            3 => array(4),
            4 => array(5),
        );

        $arr = array();

        foreach ($dElements as $dId => $dArr) {
            $dayMenu = '';
            foreach ($dArr as $dRow) {
                $element = $xpath->query(
                    '/html/body/div[2]/div[2]/article/div/div/p['.$dRow.']'
                );

                foreach ($element as $e) {
                    $dayMenuExploded = explode(PHP_EOL, $e->nodeValue);
                    $foodArray = array();
                    foreach ($dayMenuExploded as $foods) {
                        $foods = parent::cleanStr($foods);
                        if (!empty($foods)) {
                            $foodArray[] = preg_replace('/([\t0-9, ]+$)/', '', $foods);
                        }
                    }
                    $dayMenu = implode(' / ', $foodArray);
                }
            }
            $weekDay = parent::weekNumToText($dId);
            $arr[$weekDay] = parent::fixSpaces(preg_replace('/[0-9, ]+$/', '', $dayMenu));
        }

        if (!empty($arr)) {
            return $arr;
        }

        return false;
    }
}
