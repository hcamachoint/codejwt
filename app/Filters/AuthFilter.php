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
      helper('jwt');
      if (!$request->hasHeader('Authorization')){
          return Services::response()->setStatusCode(401, 'Unauthorized');
      }else {
          $token = $request->getHeader('Authorization')->getValue();
          $check = checkToken($token);
          if ($check[0] == 400 || $check[0] == 401) {
            return Services::response()->setStatusCode($check[0], $check[1]);
          }
      }


      /*$cache = \Config\Services::cache();
      if (!empty($cache->get('auth_token'))) {
        $almacen = $cache->get('auth_token');
        if (in_array($token, $almacen)) {
          return Services::response()->setStatusCode(401, 'Unauthorized');
        }
      }*/
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response)
    {
      return $response;
    }
}
