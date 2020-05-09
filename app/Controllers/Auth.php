<?php namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use App\Models\UserModel;

class Auth extends BaseController
{
	use ResponseTrait;

	public function login()
	{
		$model = new UserModel();
		$userLog = $this->request->getJSON();
		$user = $model->login($userLog->username, $userLog->password);

		if ($user[0] === false) {
			return $this->failNotFound();
		}else {
			helper('kit');
	    $key = getenv('app.key');
			$token = array(
					'aud' => aud(),
					'data' => [
	        	'id' => $user[1]['id'],
	        	'username' => $user[1]['username'],
	          'email' => $user[1]['email']
	    		]
			);
			$jwt = JWT::encode($token, $key);
			return $this->respond($jwt, 200);
		}

    //with filters check token if valid for routes

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
