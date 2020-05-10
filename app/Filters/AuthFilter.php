<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Config\Services;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request)
    {
      if (empty($request->getHeader('Authorization'))) {
        return Services::response()->setStatusCode(400, 'Token required');
      }

      helper('token');
      $token = $request->getHeader('Authorization')->getValue();
      $res = checkToken($token);

      if ($res[0] == 400 || $res[0] == 401) {
        return Services::response()->setStatusCode($res[0], $res[1]);
      }
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response)
    {
      return $response;
    }
}
