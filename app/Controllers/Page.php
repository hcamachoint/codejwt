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

	public function test()
	{
		//STORE USER TOKEN TO VERIFY FROM HERE AND INVALIDATE TOKEN
		$token[] = 'token1';
		cache()->save('auth_token', $token, 2592000);
	}

	public function test2()
	{
		$new = 'token2';
		$cache = \Config\Services::cache();
		$almacen = $cache->get('auth_token');
		$almacen[] = $new;
		cache()->save('auth_token', $almacen, 2592000);
		print_r($cache->get('auth_token'));
		//$cache->delete('foo');
	}

	public function test3()
	{
		$cache = \Config\Services::cache();
		$almacen = $cache->get('auth_token');

		if (array_search('token2', $almacen)) {
			print_r("Si esta");
		}else {
			print_r("No esta");
		}

	}
}
