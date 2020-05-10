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
    if ($blogs) {
      return $this->respond($blogs, 200);
    }
    return $this->failNotFound();

	}

  public function view($id)
	{
    $blogModel = new BlogModel();
    $blog = $blogModel->find($id);
    if ($blog) {
      return $this->respond($blog, 200);
    }
    return $this->failNotFound();

	}

  public function search($keyword)
	{
    $blogModel = new BlogModel();
    $blogs = $blogModel->like('title', $keyword, 'both')->findAll();
    if ($blogs) {
      return $this->respond($blogs, 200);
    }
    return $this->failNotFound();
	}

  public function create()
  {
    $blogInfo = $this->request->getJSON();
    $blogModel = new BlogModel();
    try {
      $blogModel->insert($blogInfo);
      return $this->respondCreated($blogInfo);
    } catch (\Exception $e) {
      return $this->failServerError($e);
    }
  }

  public function update()
  {
    $blog = $this->request->getJSON();
    $blogModel = new BlogModel();

    if ($blogModel->find($blog->id)) {
      try {
        $blogModel->update($blog->id, $blog);
        return $this->respond($blog, 200);
      } catch (\Exception $e) {
        return $this->failServerError($e);
      }
    }
    return $this->failNotFound();
  }

  public function delete($id)
	{
    $blogModel = new BlogModel();
    if ($blogModel->find($id)) {
      try {
        $blogModel->delete($id);
        return $this->respondDeleted();
      } catch (\Exception $e) {
        return $this->failServerError($e);
      }
    }
    return $this->failNotFound();
	}
}
