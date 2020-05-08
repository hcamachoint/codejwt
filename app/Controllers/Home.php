<?php namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
    if ($this->request->getMethod() == 'get') {
			echo json_encode(['data' => 'Main Page JSON']);
		}
	}
}
