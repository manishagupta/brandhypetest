<?php namespace App\Controllers;
 
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
 
class Users extends ResourceController
{
    use ResponseTrait;
    // get all Users
    public function index()
    {
		$throttler = \Config\Services::throttler();

		// Checking login attempt 4 times in a minute
        $allowed = $throttler->check('login', 4, MINUTE);
		
		if ($allowed) { // if form_submitted <= 4 
			$model = new UserModel();
			$data = $model->findAll();
			return $this->respond($data);
			//do your login process
        } else {
			
            //return requested too many timeserror 
            return $this->failNotFound("error", "You requested too many times");
        }

    }
 
}