<?php namespace App\Controllers;

use App\Models\BlogModel;
use CodeIgniter\API\ResponseTrait;

class Blog extends BaseController
{
  use ResponseTrait;

	public function index()
	{
    $blogModel = new BlogModel();
    $blogs = $blogModel->findAll();
    return $this->response->setStatusCode(200)->setJSON($blogs);
	}

  public function view($id)
	{
    $blogModel = new BlogModel();
    $blog = $blogModel->find($id);
    return $this->response->setStatusCode(200)->setJSON($blog);
	}

  public function search($keyword)
	{
    $blogModel = new BlogModel();
    $blogs = $blogModel->like('title', $keyword, 'both')->findAll();
    return $this->response->setStatusCode(200)->setJSON($blogs);
	}

  public function create()
  {
    $blogInfo = $this->request->getJSON();
    /*$blog = [ //FOR CUSTOM, INSERT THIS $BLOG
      'title' => $blogInfo->title,
      'description' => $blogInfo->description
    ];*/
    $blogModel = new BlogModel();
    $blogModel->insert($blogInfo);
    return $this->response->setStatusCode(200);
  }

  public function update()
  {
    $blog = $this->request->getJSON();
    $blogModel = new BlogModel();
    $blogModel->update($blog->id, $blog);
    return $this->response->setStatusCode(200);
  }

  public function delete($id)
	{
    $blogModel = new BlogModel();
    $blogModel->delete($id);
    return $this->response->setStatusCode(200)->setJSON($blog);
	}
}
