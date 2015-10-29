<?php

class Olearys extends Lunch
{
    protected $url = "http://www.olearys.fi/forumhelsinki/menu/mainmenu";
    protected $postData = array();
    protected $referer = "";
    protected $gzipped = TRUE;

    protected function HTMLtoLunchArray($html)
    {
        $doc = new DOMDocument();
        @$doc->loadHTML($html);

        $xpath = new DOMXpath($doc);

        $elements = $xpath->query(
            '//*[@id="ctl00_ContentPlaceHolder1_TemplateArea_TC30900_CellContent"]/div/div[2]/div[2]/div/div/span[*]/span[2]/span[2]'
        );

        $arr = array();
        $i = 0;

        if (!is_null($elements)) {
            foreach ($elements as $element) {
                $nodes = $element->childNodes;
                foreach ($nodes as $node) {
                    $weekDay = parent::weekNumToText($i);
                    $arr[$weekDay] = trim(utf8_decode($node->nodeValue));
                    $i++;
                }
            }
        }

        if (!empty($arr)) {
            return $arr;
        }

        return false;
    }
}