<?php

use Hashids\Hashids;

function idtohash($id){
    $hasids = new Hashids('magicpay123!@#',8);
    return $hasids->encode($id);
}

function hashtoid($hash){
    $hasids = new Hashids('magicpay123!@#',8);
    return $hasids->decode($hash)[0];

}
?>
