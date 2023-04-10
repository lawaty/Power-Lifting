<?php

class Curl {
  private static function format($code, $body){
    return array(
      'code'=>$code,
      'body'=>$body
    );
  }

  public static function post($url, $arr){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_TIMEOUT,10);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($arr));
    $body = curl_exec($ch);
    $code = curl_getMeta($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return self::format($code, $body) ;
  }
}

?>