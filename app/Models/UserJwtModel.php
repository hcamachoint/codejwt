<?php namespace App\Models;

use CodeIgniter\Model;

class UserJwtModel extends Model
{
    protected $table = 'users_jwt';
    protected $primaryKey = 'id';
    protected $allowedFields = ['token', 'user'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
