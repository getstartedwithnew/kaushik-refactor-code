<?php

foreach (explode("\n", file_get_contents($argv[1])) as $row) {

    if (empty($row)) break;
    $p = explode(",",$row);
    $p2 = explode(':', $p[0]);
    $value[0] = trim($p2[1], '"');
    $p2 = explode(':', $p[1]);
    $value[1] = trim($p2[1], '"');
    $p2 = explode(':', $p[2]);
    $value[2] = trim($p2[1], '"}');

    $binResults = file_get_contents('https://lookup.binlist.net/' .$value[0]);
    if (!$binResults)
        die('error!');
    $r = json_decode($binResults);
    $isEu = isEu($r->country->alpha2);

    $result = json_decode(file_get_contents('https://api.exchangeratesapi.io/latest'),true);
    if(isset($result['success']) && $result['success'] != "") {
        
        $rate = $result['rates'][$value[2]];
        if ($value[2] == 'EUR' or $rate == 0) {
            $amntFixed = $value[1];
        }
        if ($value[2] != 'EUR' or $rate > 0) {
            $amntFixed = $value[1] / $rate;
        }

        echo $amntFixed * ($isEu == 'yes' ? 0.01 : 0.02);
        print "\n";
    
    } else {
        if(isset($result['error'])) {
            die($result['error']['info']);
        } else {
            die('no result from exchange api');
        }
    }
}

function isEu($c) {
    $euArray = array('AT','BE','BG','CY','CZ','DE','DK','EE','ES','FI','FR','GR','HR','HU','IE','IT','LT','LU','LV','MT','NL','PO','PT','RO','SE','SI','SK');
    if(array_search($c,$euArray)) {
        return "yes";
    } else {
        return false;
    }
}
