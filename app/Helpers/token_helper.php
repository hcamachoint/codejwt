<?php

use Firebase\JWT\JWT;
use Config\Services;

function aud(){
    $aud = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $aud = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $aud = $_SERVER['REMOTE_ADDR'];
    }

    $aud .= @$_SERVER['HTTP_USER_AGENT'];
    $aud .= gethostname();
    return sha1($aud);
}

function dataToken($request){
  if (empty($request->getHeader('Authorization'))) {
    return Services::response()->setStatusCode(401, 'Unauthorized');
  }else {
    $token = $request->getHeader('Authorization')->getValue();
    return JWT::decode($token,getenv('app.key'), ['HS256'])->data;
  }

}

function checkToken($token){
  try {
    $decoded = JWT::decode($token, getenv('app.key'), ['HS256']);
  } catch (\Exception $e) {
    return array(400, $e->getMessage());
  }

  if($decoded->aud !== aud()){
    return array(401, 'Unauthorized!');
  }

  return array(200, 'Ok');
}

function getToken($request){
  if (empty($request->getHeader('Authorization'))) {
    return Services::response()->setStatusCode(401, 'Unauthorized');
  }else {
    return $request->getHeader('Authorization')->getValue();
  }
}
