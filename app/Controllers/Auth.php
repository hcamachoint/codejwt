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
			helper('token');
	    $key = getenv('app.key');
			$token = array(
					'aud' => aud(),
					'iat' => time(),
					'exp' => time() + (86400),
					'data' => [
	        	'id' => $user[1]['id'],
	        	'username' => $user[1]['username'],
	          'email' => $user[1]['email']
	    		]
			);
			$jwt = JWT::encode($token, $key);
			return $this->respond($jwt, 200);
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
