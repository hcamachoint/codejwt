<?php namespace App\Controllers;

header('Content-type: application/json');

class Api extends BaseController
{
	public function index()
	{
    if ($this->request->getMethod() == 'get') {
			echo json_encode(['data' => 'This is GET response!']);
		}
	}
}
