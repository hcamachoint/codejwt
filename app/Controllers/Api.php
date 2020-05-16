<?php namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use App\Models\UserModel;
use App\Models\UserSessionModel;
use Config\Services;

class Api extends BaseController
{
	use ResponseTrait;

	public function __construct()
  {
     helper('jwt');
		 helper('cookie');
  }

	public function login()
	{
		$model = new UserModel();
		$userLog = $this->request->getJSON();

		$validation =  \Config\Services::validation();
		$validation->setRules([
				'username' => ['label' => 'Username', 'rules' => 'required|alpha_numeric|min_length[3]|max_length[50]'],
				'password' => ['label' => 'Password', 'rules' => 'required|min_length[6]|max_length[100]']
		]);
		if (!$validation->run((array)$userLog)) {
			$errors = $validation->getErrors();
			return $this->fail($errors, 400);
		}

		$user = $model->login($userLog->username, $userLog->password);

		if ($user[0] === false) {
			return $this->fail($user[1], 400);
		}else {
			$token = array(
					'aud' => aud(),
					'exp' => time() + ((int)getenv('jwt.token_auth_expiry')),
					'data' => [
	        	'id' => $user[1]['id'],
	        	'username' => $user[1]['username'],
	          'email' => $user[1]['email']
	    		]
			);
			$refresh = array(
					'aud' => aud(),
					'exp' => time() + ((int)getenv('jwt.token_refresh_expiry')),
			);

			$jwt = JWT::encode($token, getenv('jwt.token_key'));
			$jwt_refresh = JWT::encode($refresh, getenv('jwt.token_key'));

			$jwt_storage = new UserSessionModel();
			$jwt_storage->insert(['token' => $jwt_refresh, 'user' => $user[1]['id']]);

			return $this->respond(['token' => $jwt, 'refresh_token' => $jwt_refresh], 200);
		}
	}

	public function refresh()
	{
		$origin_token_auth = $this->request->getHeader('Authorization')->getValue();
		$origin_token_refresh = $this->request->getCookie('refresh_token');

		if (getenv('jwt.token_blacklist') == 'true') {
			$cache = \Config\Services::cache();
			if (!empty($cache->get('refresh_token'))) {
				$almacen2 = $cache->get('refresh_token');
				if (in_array($origin_token_refresh, $almacen2)) {
					return $this->fail('Unauthorized', 401);
				}
			}
		}

		$data = (array)dataToken($origin_token_auth);
		$token = array(
				'aud' => aud(),
				'exp' => time() + ((int)getenv('jwt.token_auth_expiry')),
				'data' => [
        	'id' => $data['id'],
        	'username' => $data['username'],
          'email' => $data['email']
    		]
		);
		$refresh = array(
				'aud' => aud(),
				'exp' => time() + ((int)getenv('jwt.token_refresh_expiry')),
		);

		$jwt = JWT::encode($token, getenv('jwt.token_key'));
		$jwt_refresh = JWT::encode($refresh, getenv('jwt.token_key'));

		try {
			$jwt_storage = new UserSessionModel();
			$jwt_storage->where('token', $origin_token_refresh)->set(['token' => $jwt_refresh])->update();
		} catch (\Exception $e) {
			print_r($e->getMessage());
		}

		//IVALIDATE TOKENS
		if (getenv('jwt.token_blacklist') == 'true') {
			if (!empty($cache->get('auth_token'))) {
				$almacen = $cache->get('auth_token');
			  $almacen[] = $origin_token_auth;
				cache()->save('auth_token', $almacen, getenv('jwt.token_auth_expiry'));
			}else {
				$almacen[] = $origin_token_auth;
			  cache()->save('auth_token', $almacen, getenv('jwt.token_auth_expiry'));
			}
			if (!empty($cache->get('refresh_token'))) {
				$almacen2 = $cache->get('refresh_token');
				$almacen2[] = $origin_token_refresh;
				cache()->save('refresh_token', $almacen2, getenv('jwt.token_refresh_expiry'));
			}else {
				$almacen2[] = $origin_token_refresh;
			  cache()->save('refresh_token', $almacen2, getenv('jwt.token_refresh_expiry'));
			}
		}

		//set_cookie(['name' => 'refresh_token', 'value' => $jwt_refresh, 'httponly' => TRUE]);
		return $this->respond(['token' => $jwt, 'refresh_token' => $jwt_refresh], 200);

	}

	public function logout()
	{
	  $cache = \Config\Services::cache();
		$token = $this->request->getHeader('Authorization')->getValue();
		$refresh_token = $this->request->getCookie('refresh_token');

		//DELETE FROM COOKIE
		delete_cookie('refresh_token');

		//DELETE FROM DATABASE
		$jwt_storage = new UserSessionModel();
		if ($jwt_storage->where('token', $refresh_token)) {
      try {
        $jwt_storage->where('token', $refresh_token)->delete();
      } catch (\Exception $e) {
				print_r($e->getMessage());
      }
    }

		//ADD TO BLACKLIST
		if (getenv('jwt.token_blacklist') == 'true') {
			if (!empty($cache->get('auth_token'))) {
				$almacen = $cache->get('auth_token');
			  $almacen[] = $token;
				cache()->save('auth_token', $almacen, (int)getenv('jwt.token_auth_expiry'));
			}else {
				$almacen[] = $token;
			  cache()->save('auth_token', $almacen, (int)getenv('jwt.token_auth_expiry'));
			}

			if (!empty($cache->get('refresh_token'))) {
				$almacen2 = $cache->get('refresh_token');
				$almacen2[] = $refresh_token;
				cache()->save('refresh_token', $almacen2, (int)getenv('jwt.token_refresh_expiry'));
			}else {
				$almacen2[] = $refresh_token;
			  cache()->save('refresh_token', $almacen2, (int)getenv('jwt.token_refresh_expiry'));
			}
		}
	}

	public function register()
	{
		helper('kit');
		$userModel = new UserModel();
		$userInfoJson = $this->request->getJSON();

		$validation =  \Config\Services::validation();
		$validation->setRules([
				'firstname' => ['label' => 'Firstname', 'rules' => 'required|alpha|min_length[3]|max_length[30]'],
				'lastname' => ['label' => 'Lastname', 'rules' => 'required|alpha|min_length[3]|max_length[30]'],
				'username' => ['label' => 'Username', 'rules' => 'required|alpha_numeric|is_unique[users.username]|min_length[3]|max_length[50]'],
				'email' => ['label' => 'Email', 'rules' => 'required|valid_email|is_unique[users.email]|min_length[5]|max_length[50]'],
				'password' => ['label' => 'Password', 'rules' => 'required|min_length[6]|max_length[100]'],
				'password_confirm' => ['label' => 'Password Confirm', 'rules' => 'required|min_length[6]|max_length[100]|matches[password]']
		]);
		if (!$validation->run((array)$userInfoJson)) {
			$errors = $validation->getErrors();
			return $this->fail($errors, 400);
		}

		$userInfoFixed = [
			'uuid' => uuid(),
      'firstname' => $userInfoJson->firstname,
      'lastname' => $userInfoJson->lastname,
			'username' => $userInfoJson->username,
			'email' => $userInfoJson->email,
			'password' => password_hash($userInfoJson->password, PASSWORD_BCRYPT),
			'status' => 1
    ];

		try {
      $userModel->insert($userInfoFixed);
      return $this->respondCreated($userInfoFixed);
    } catch (\Exception $e) {
      return $this->failServerError($e->getMessage());
    }
	}
}
