<?php namespace App\Controllers;
 
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use \Firebase\JWT\JWT;

class Users extends ResourceController
{
    use ResponseTrait;
    
	// Code to generate JWT access token
    public function index()
    {
		$model = new UserModel();
		$data = $model->findAll();
		$key = getenv('JWT_SECRET');// JWT secret key from env file
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
		$token = JWT::encode($payload, $key); // token generation
    }
	
	/*	
	Function : getUserInfo
	Purpose : get information of a user
	Params : user_id
	URL: http://localhost:8080/users/getUserInfo?user_id=user_id
	*/
	public function getUserInfo(){
		$model = new UserModel();
		$user_id=$this->request->getVar('user_id');
		$data = $model->getWhere(['id' => $user_id])->getResult(); //get user data	
        if($data){ //check if data found
            return $this->respond($data);
        }else{
            return $this->failNotFound('No Data Found with this user id.');
        }		
	}
	
	/*	
	Function : showBalance
	Purpose : find out user current balance 
	Authorization : bearer token
	URL: http://localhost:8080/users/showBalance
	*/
	public function showBalance(){
		$headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
		$throttler = \Config\Services::throttler(); //intialize throttler 
		if (!empty($headers)) { // check if header is present
			if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
				$access_token= $matches[1];
			}
			$allowed = $throttler->check($access_token, 50, 3600); // checking 50 request in an hour
			
			if ($allowed) { // if request <= 50
				$model = new UserModel();
				$data = $model->select(['total_balance'])->getWhere(['access_token' => $access_token])->getResult();
				return $this->respond($data);
			} else {				
				//return requested too many timeserror 
				return $this->failNotFound("error", "You requested too many times.");
			}
		} else {
			return $this->failNotFound('Missing information.');
		}
	}
 
}