<?php namespace App\Controllers;

class Api extends BaseController
{
	public function index()
	{
    if ($this->request->getMethod() == 'get') {
			echo json_encode(['data' => 'This is GET response!']);
		}
	}
}
