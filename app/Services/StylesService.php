<?php
namespace App\Services;
/**
 *  Compute Styles from non style values
 */
class StylesService
{
    public function getBidStatusLabelColor($status){
      switch ($status) {
        case 'completed':
          return 'success';
          break;
        case 'pending':
          return 'default';
          break;
        case 'initiated':
          return 'info';
          break;
        default:
          return 'default';
          break;
      }
    }
}
