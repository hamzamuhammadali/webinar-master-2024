<?php defined( 'ABSPATH' ) || exit;

if ( ! function_exists('webinarignition_write_log')) {
   function webinarignition_write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}