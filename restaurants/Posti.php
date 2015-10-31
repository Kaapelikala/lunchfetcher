<?php

class Posti extends Lunch
{
    protected $url = "http://www.fazer.fi/api/location/menurss/current?pageId=900&language=fi";
    protected $postData = array();
    protected $referer = "";
    protected $gzipped = false;
    protected $enabled = true;

    protected function HTMLtoLunchArray($html)
    {
        $xml = simplexml_load_string($html);

        foreach ($xml->channel as $channel)
        {
            foreach ($channel->item as $item)
            {
                $data = $item->description;

                $doc = new DOMDocument();
                @$doc->loadHTML($data);
                $xpath = new DOMXpath($doc);

                $elements = $xpath->query(
                    '//p'
                );

                $foodArrs = array();
                $i = -1;

                if (!is_null($elements)) {
                    foreach ($elements as $element) {
                        $nodes = $element->childNodes;
                        foreach ($nodes as $node) {
                            if (preg_match('/Maanantai|Tiistai|Keskiviikko|Torstai|Perjantai/i', $node->nodeValue)) {
                                $i++;
                            } elseif (preg_match('/tervetuloa|P.*iv.*n j.*lkiruoka|pienet muutokset/i', $node->nodeValue)) {
                                continue;
                            } else {
                                if (strlen($node->nodeValue) > 3) {
                                    $weekDay = parent::weekNumToText($i);
                                    $foodArrs[$weekDay][] = utf8_decode(trim($node->nodeValue));
                                }
                            }
                        }
                    }
                }

                $arr = array();
                foreach ($foodArrs as $weekDay => $foodArr)
                {
                    $arr[$weekDay] = implode(' / ', $foodArr);
                }

               continue 2;
            }
        }

        if (!empty($arr)) {
            return $arr;
        }

        return false;
    }
}
