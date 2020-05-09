<?php namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;

class Page extends BaseController
{
	use ResponseTrait;

	public function index()
	{
		return $this->respond(['data' => 'Main Page JSON'], 200);
	}

	public function home()
	{
		return $this->respond(['data' => 'Home Page JSON'], 200);
	}

	public function test()
	{
		print_r($this->request->getHeader('Authorization'));
	}
}
