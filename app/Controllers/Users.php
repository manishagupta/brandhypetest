<?php namespace App\Controllers;
 
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use \Firebase\JWT\JWT;

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
			$key = getenv('JWT_SECRET');
			//var_dump( $key);
			$iat = time(); // current timestamp value
			$exp = $iat + 3600;
	 
			$payload = array(
				"iss" => "Issuer of the JWT",
				"aud" => "Audience that the JWT",
				"sub" => "Subject of the JWT",
				"iat" => $iat, //Time the JWT issued at
				"exp" => $exp, // Expiration time of token
				"email" => 'manisha7971@gmail.com',
			);
			/* 
			$token = JWT::encode($payload, $key);
			*/
			return $this->respond($data);
        } else {
			
            //return requested too many timeserror 
            return $this->failNotFound("error", "You requested too many times");
        }

    }
	
	public function showdata(){
		$headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
		$throttler = \Config\Services::throttler();

		$allowed = $throttler->check($headers, 4, 60);
		print_r($allowed);
		if ($allowed) { // if form_submitted <= 4 
			$model = new UserModel();
			$data = $model->findAll();
			return $this->respond($data);
        } else {
			
            //return requested too many timeserror 
            return $this->failNotFound("error", "You requested too many times");
        }
	}
 
}