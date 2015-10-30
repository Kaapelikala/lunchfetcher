<?php

$data = '<p><strong>Pienet muutokset listaan mahdollisia.</strong></p>
<p><strong>Maanantai</strong><br />Burgundinpataa (G, L, M, VS, *)<br />Höyrytettyä tummaa riisiä (G, L, M)<br />Aasialaisia linssipihvejä (A, L, M, VS, &nbsp;*)<br />Paahdettuja perunoita (G, L, M, Veg)<br />Avokado-majoneesikastiketta (A, G, L, M, VS)<br /><br />Bataattisosekeittoa (G, L, VS )</p>
<p>Päivän jälkiruoka</p>
<p><br /><strong>Tiistai</strong><br />Savukalalasagnea (A, L, VS)</p>
<p>Maissi-bataatticurrya (G, &nbsp;L, M, Veg)<br />Höyrytettyä tummaa riisiä (G, L, M, Veg)</p>
<p>Palsternakka-inkiväärisosekeittoa (A, G, L)</p>
<p>Paahdettua mantelirouhetta (G, L, M)</p>
<p>Päivän jälkiruoka</p>
<p><br /><strong>Keskiviikko</strong><br />Jauhelihapihvejä pekoni-tomaattikastikkeessa (A, L, M, VS)<br />Perunasosetta (A, G, L, *)</p>
<p>Juusto-kasvispastaa (A, L, VS)</p>
<p>Täyteläistä mustajuurisosekeittoa (A, G, L)</p>
<p>Päivän jälkiruoka</p>
<p><strong>Torstai</strong> <br />Kanaa mantelikastikkeessa (A, G, L, VS)<br />Luomutofua ja kasviksia kormakastikkeessa (A, G, L, VS, *)<br />Jasminriisiä (G, L, M)</p>
<p>Perinteistä hernekeittoa (A, G, L, M)</p>
<p>Pannukakkua (A)<br />Mansikkahilloa (G, L, M)<br />Kermavaahtoa (A, G, L)</p>
<p><br /><strong>Perjantai</strong></p>
<p>Glaseerattua porsaanniskaa (A, G, L, M)<br />Yrtti-lohkoperunoita (G, L, M, Veg)<br />Höyrytettyä tummaa riisiä (G, L, M)<br />Tomaattista munakoisopataa (G, L, M, VS, Veg, *)<br />Paahdettuja porkkanoita (G, L, M)</p>
<p>Tomaattikeittoa ja fetajuustoa (A, G, VS, VL)</p>
<p>Päivän jälkiruoka</p>
<p><strong>Tervetuloa!</strong></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>%                                                                                                                                                                                               xy)
';

        $doc = new DOMDocument();
        @$doc->loadHTML($data);
        $xpath = new DOMXpath($doc);

        $elements = $xpath->query(
            '//p'
        );

        $x = array();
        $i = 0;

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
                            $x[$i][] = trim($node->nodeValue);
                        }
                    }
                }
            }
        }

var_dump($x);