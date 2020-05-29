<?php namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use App\Models\UserModel;

class User extends BaseController
{
	use ResponseTrait;

  public function __construct()
  {
     helper('jwt');
  }

	public function profile()
	{
		$token = $this->request->getHeader('Authorization')->getValue();
    return $this->respond(dataToken($token), 200);
  }

  public function disconnect()
  {
    $data = dataToken($this->request->getHeader('Authorization')->getValue());
    $model = new UserModel();

    if ($model->find($data->id)) {
      try {
        $model->delete($data->id);
        return $this->respondDeleted("Account deleted!");
      } catch (\Exception $e) {
        return $this->failServerError($e->getMessage());
      }
    }
    return $this->failNotFound();
  }

  public function update()
  {
    $data = dataToken($this->request->getHeader('Authorization')->getValue());

    $userInfoJson = $this->request->getJSON();
    $userInfoFixed = [
      'firstname' => $userInfoJson->firstname,
      'lastname' => $userInfoJson->lastname
    ];

    $validation =  \Config\Services::validation();
    $validation->setRules([
				'firstname' => ['label' => 'Firstname', 'rules' => 'required|alpha|min_length[3]|max_length[30]'],
				'lastname' => ['label' => 'Lastname', 'rules' => 'required|alpha|min_length[3]|max_length[30]'],
		]);
		if (!$validation->run($userInfoFixed)) {
			$errors = $validation->getErrors();
			return $this->fail($errors, 400);
		}

    $model = new UserModel();
    if ($model->find($data->id)) {
      try {
        $model->update($data->id, $userInfoFixed);
        return $this->respond($userInfoFixed, 200);
      } catch (\Exception $e) {
        return $this->failServerError($e);
      }
    }
    return $this->failNotFound();
  }

  public function password()
  {
    $data = dataToken($this->request->getHeader('Authorization')->getValue());

    $userInfoJson = $this->request->getJSON();
    $validation =  \Config\Services::validation();
    $validation->setRules([
      'password' => ['label' => 'Password', 'rules' => 'required|min_length[6]|max_length[100]'],
      'password_confirm' => ['label' => 'Password Confirm', 'rules' => 'required|min_length[6]|max_length[100]|matches[password]']
		]);
		if (!$validation->run((array)$userInfoJson)) {
			$errors = $validation->getErrors();
			return $this->fail($errors, 400);
		}

    $newpass = ['password' => password_hash($userInfoJson->password, PASSWORD_BCRYPT)];

    $model = new UserModel();
    if ($model->find($data->id)) {
      try {
        $model->update($data->id, $newpass);
        return $this->respond(['message' => 'Ok'], 200);
      } catch (\Exception $e) {
        return $this->failServerError($e);
      }
    }
    return $this->failNotFound();
  }
}
