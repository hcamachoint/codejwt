<?php namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;

class Home extends BaseController
{
	use ResponseTrait;

	public function login()
	{
    helper('kit');
    $key = getenv('app.key');

    //model->auth(username, password): return userData to use on token
    //with filters check token if valid for routes
    //https://anexsoft.com/implementacion-de-json-web-token-con-php
		$token = array(
				'aud' => aud(),
				'data' => [
        	'id' => 1, //model->id
        	'username' => 'alejandro' //model->username
          'email' => 'admin@local.dev' //model->email
    		]
		);
		return $this->respond(['data' => 'Main Page JSON'], 200);
	}

	public function test()
	{
		helper('kit');
		//$time = time();
		$key = getenv('app.key');
		$token = array(
		    //'iat' => $time,
		    //'exp' => $time + (60*60),
				'aud' => aud(),
				'data' => [
        	'id' => 1,
        	'name' => 'Alejandro'
    		]
		);

		$jwt = JWT::encode($token, $key);
		$data = JWT::decode($jwt, $key, array('HS256'));

		print_r($data);
	}
}
