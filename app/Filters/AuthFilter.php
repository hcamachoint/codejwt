<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request)
    {
      //GET SESSION TOKEN FROM HEADER
      $request->getHeader('host');
      if (!session()->logged_in)
      {
          return redirect()->to(base_url().'/auth/login');
      }
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response)
    {
        return $response;
    }
}
