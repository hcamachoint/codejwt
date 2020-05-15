<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['uuid', 'firstname', 'lastname', 'username', 'email', 'password', 'status'];
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function login($username, $password)
    {
      try {
        $query = $this->where("username", $username)->first();
      } catch (\Exception $e) {
        return array(false, 'Something went wrong!');
      }

      if ($query) {
        if (password_verify($password, $query['password'])) {
          if ($query['status'] == 9) {
            return array(false, 'Your account is banned!');
          }else{
            $userdata = [
                    'id'  => $query['id'],
                    'username'  => $query['username'],
                    'email'  => $query['email'],
                    'logged_in' => TRUE
            ];
            return array(true, $userdata);
          }
        }
      }
      return array(false, 'Wrong username or password!');
    }
}
