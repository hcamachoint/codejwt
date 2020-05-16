<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Config\Services;

class AuthFilter implements FilterInterface
{
    use ResponseTrait;

    public function before(RequestInterface $request)
    {
      helper('jwt');
      if (!$request->hasHeader('Authorization')){
          return Services::response()->setStatusCode(401, 'Unauthorized');
      }else {
          $token = $request->getHeader('Authorization')->getValue();
          try {
            $decoded = JWT::decode($token, getenv('jwt.token_key'), ['HS256']);
          } catch (\Exception $e) {
            return Services::response()->setStatusCode(400, $e->getMessage());
          }
          if($decoded->aud !== aud())Services::response()->setStatusCode(401, 'Unauthorized!');
      }

      if (getenv('jwt.token_blacklist') == 'true') {
        $cache = \Config\Services::cache();
        if (!empty($cache->get('auth_token'))) {
          $almacen = $cache->get('auth_token');
          if (in_array($token, $almacen)) {
            return Services::response()->setStatusCode(401, 'Unauthorized');
          }
        }
      }
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response)
    {
      return $response;
    }
}
