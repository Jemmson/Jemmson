<?php
namespace App\Services;
/**
 *  Compute Styles from non style values
 */
class UpdateRecordsService
{
    public static function shouldUpdate($record){
      if($record == null || $record == ''){
        return false;
      }
      return true;
    }
}
