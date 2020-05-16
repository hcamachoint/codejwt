<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Config\Services;

class GuestFilter implements FilterInterface
{
    public function before(RequestInterface $request)
    {
      if ($request->hasHeader('Authorization')) {
        return Services::response()->setStatusCode(403, "Forbidden");
      }
    }

    public function after(RequestInterface $request, ResponseInterface $response)
    {
        return $response;
    }
}
