<?php

namespace App\Controllers;
use Framework\Database;
use Framework\Validation;
use Framework\Session;

class UserController{
    protected $db;

    public function __construct(){
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    /**
     * Show the login page
     */
    public function login(){
        loadView('users/login');
    }

    /**
     * Show the register page
     */
    public function create(){
        loadView('users/create');
    }

    /**
     * Store user in db
     * @return void
     */
    public function store(){
        $name = $_POST['name'];
        $email = $_POST['email'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $password = $_POST['password'];
        $passwordConfirmation = $_POST['password_confirmation'];

        $errors = [];

        if(!Validation::email($email)){
            $errors['email'] = 'Please enter a valid email address';
        }

        if(!Validation::string($name,2, 50)){
            $errors['name'] = 'Name must be between 2 and 50 characters';
        }

        if(!Validation::string($password,6, 50)){
            $errors['password'] = 'Password must be at least 6 characters long';
        }

        if(!Validation::match($password, $passwordConfirmation)){
            $errors['password_confirmation'] = 'Passwords do not match ';
        }

        //inspect($errors);

        if(!empty($errors)){
            loadView('users/create', [
                'errors' => $errors,
                'user' => [
                    'name' => $name,
                    'email' => $email,
                    'city' => $city,
                    'state' => $state
                ]
            ]);
            exit;
        }else{
            //inspectAndDie('Store');

            //Check if email exists;
            $params = [
                'email' => $email
            ];

            $user = $this->db->query('SELECT * FROM workopia.users WHERE email = :email',$params)->fetch();

            if($user){
                inspect($user);
                $errors['email'] = 'That email already exists';
                loadView('users/create', [
                    'errors' => $errors,
                    'user' => [
                        'name' => $name,
                        'email' => $email,
                        'city' => $city,
                        'state' => $state
                    ]
                ]);
                exit;
            }

            //Insert into db
            $params = [
                'name' => $name,
                'email' => $email,
                'city' => $city,
                'state' => $state,
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ];

            $this->db->query('INSERT INTO workopia.users (name, email, city, state, password) VALUES (:name,:email,:city,:state,:password)', $params);

            //get new user id
            $userId = $this->db->conn->lastInsertId();

            Session::set('user',[
                "id" => $userId,
                "name" => $name,
                "email" => $email,
                "city" => $city,
                "state" => $state,

            ]);

            //inspectAndDie(Session::get('user'));

            redirect('/');
        }
    }


    /**
     * Clearing the session and cookie during logout
     * @return void
     */
    public function logout(){
        // Session::clear('user');
        Session::clearAll();

        $params = session_get_cookie_params(); 
        setcookie('PHPSESSID','',time() - 86400, $params['path'], $params['domain']);

        //redirecting 
        redirect('/');
    }

    /**
     * Authenticate user with email and password
     * @return void
     */
    public function authenticate(){
        //inspectAndDie('Login');

        $email = $_POST['email'] ;
        $password = $_POST['password'];

        //inspect($email);
        //inspect($password);

        $errors = [];

        if(!Validation::email($email)){
            $errors['email'] = 'Please enter a valid email';
        }

        if(!Validation::string($password,6,50)){
            $errors['password'] = 'Password must be at least 6 character';
        }

        if(!empty($errors)){
            loadView('users/login',[
                'errors' => $errors
            ]);
            exit;
        }

        //Check for email
        $params = [
            'email' => $email
        ];

        $user = $this->db->query('SELECT * FROM workopia.users WHERE email = :email', $params)->fetch();

        if(!$user){
            $errors['email'] = 'Incorrect credentials';
            loadView('users/login',[
                'errors' => $errors
            ]);
            exit;
        }

        //Check if password is correct
        if(!password_verify($password,$user->password)){
            $errors['email'] = 'Incorrect credentials';
            loadView('users/login',[
                'errors' => $errors
            ]);
            exit;
        }

        //Set the user session
        Session::set('user',[
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email, 
            'city' => $user->city,
            'state' => $user->state,
        ]);

        redirect('/');
    }
    
};

?>