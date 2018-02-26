<?php
namespace App\Services;
/**
 *  Compute Styles from non style values
 */
class SanatizeService
{
    static function phone($phone){
        return preg_replace('/[^0-9]/', '', $phone);
    }
}
