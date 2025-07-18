<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;
use Framework\Session;

class UserController
{
    protected $db;

    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    /**
     * Show Register page
     * @return void
     */
    public function create()
    {
        loadView('users/create');
    }

    /**
     * Show Login Page
     * @return void
     */
    public function login()
    {
        loadView('users/login');
    }

    /**
     * Store User in Database
     * @return void
     */
    public function store()
    {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $password = $_POST['password'];
        $passwordConfirmation = $_POST['password_confirmation'];

        $errors = [];

        // Validation

        if (!Validation::email($email)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if (!Validation::string($name, 2, 50)) {
            $errors['name'] = 'Invalid name.';
        }

        if (!Validation::string($password, 6, 50)) {
            $errors['password'] = 'Password must be alteast 6 characters';
        }

        if (!Validation::match($password, $passwordConfirmation)) {
            $errors['password_confirmation'] = 'Passwords do not match';
        }

        if (!empty($errors)) {
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

        //Check if email exists

        $params = [
            'email' => $email
        ];

        $user = $this->db->query('SELECT * FROM users WHERE email = :email;', $params)->fetch();

        if ($user) {
            $errors = [
                'email' => 'Email already exists'
            ];

            inspectAndDie($user);
            loadView('users/create', [
                'errors' => $errors
            ]);

            exit;
        }

        //Create User Account

        $params = [
            'name' => $name,
            'email' => $email,
            'city' => $city,
            'state' => $state,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        $this->db->query('INSERT into USERS (name, email, city ,state, password) VALUES (:name, :email, :city, :state, :password);', $params);

        //Get new user ID
        $userId = $this->db->conn->lastInsertId();

        //Set User Session
        Session::set('user', [
            'id' => $userId,
            'name' => $name,
            'email' => $email,
            'city' => $city,
            'state' => $state
        ]);



        redirect('/');

    }

    /**
     * Logout User and kill session
     * 
     * @return void
     */

    public function logout()
    {
        Session::clearAll();

        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 86400, $params['path'], $params['domain']);

        redirect('/');
    }

    /**
     * Login user with email and password
     * 
     * @return void
     */

    public function auth()
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $errors = [];

        //Validation

        if (!Validation::email($email)) {
            $errors['email'] = "Please enter a valid email";
        }
        if (!Validation::string($password, 6, 50)) {
            $errors['password'] = 'Password must be atleast 6 characters';
        }

        //Check for errors
        if (!empty($errors)) {
            loadView('users/login', [
                'errors' => $errors
            ]);
            exit;
        }

        //Check for email
        $params = [
            'email' => $email
        ];

        $user = $this->db->query('SELECT * FROM users WHERE email = :email;', $params)->fetch();

        if (!$user) {
            $errors['email'] = 'Incorrect credientials';
            loadView('users/login', [
                'errors' => $errors
            ]);
            exit;
        }

        //Check if Password is correct
        if (!password_verify($password, $user->password)) {
            $errors['email'] = 'Incorrect credientials';
            loadView('users/login', [
                'errors' => $errors
            ]);
            exit;
        }

        //Set User Session
        Session::set('user', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'city' => $user->city,
            'state' => $user->state
        ]);



        redirect('/');

    }


}