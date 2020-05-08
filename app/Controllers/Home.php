<?php namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;

class Home extends BaseController
{
	use ResponseTrait;

	public function index()
	{
		return $this->respond(['data' => 'Main Page JSON'], 200);
	}

	public function test()
	{
		$key = "example_key";
		$payload = array(
		    "iss" => "http://example.org",
		    "aud" => "http://example.com",
		    "iat" => 1356999524,
		    "nbf" => 1357000000
		);

		$jwt = JWT::encode($payload, $key);
		$decoded = JWT::decode($jwt, $key, array('HS256'));

		print_r($jwt);
	}
}
