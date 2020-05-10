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
}
