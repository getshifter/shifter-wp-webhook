<?php
namespace Shifter_Webhook;
class Log{
  public static function info( string $text ) {
    self::write( $text, "INFO" );
  }
 
  public static function error( string $text ) {
    self::write( $text, "ERROR" );
  }
 
  private static function write( string $text, $log_type ) {
    $datetime = self::getDateTime();
    $text = "{$datetime} [{$log_type}] {$text}" . PHP_EOL;
    return error_log( print_r( $text, TRUE ) );
  }

  private static function getDateTime() {
    $datetime = explode( ".", microtime( true ) );
    $date = date( 'Y-m-d H:i:s', $datetime[0] );
    $time = $datetime[1];
    return "{$date}.{$time}";
  }
}