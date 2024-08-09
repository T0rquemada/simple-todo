<?php
/** @var PDO $pdo */
require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/../models/User.php';

function getBearerToken() {
    $headers = getallheaders();

    if (isset($headers['Authorization'])) {
        $authHeader = $headers['Authorization'];
        if (strpos($authHeader, 'Bearer ') === 0) {
            return substr($authHeader, 7); // Remove "Bearer"
        }
    }

    return null;
}

function validateJWT($jwt) {
    // Split the JWT into its parts
    $parts = explode('.', $jwt);

    // Check if the JWT has exactly 3 parts
    if (count($parts) !== 3) {
        return false;
    }

    return true;
}

class UserController {
    private $model;

    public function __construct() {
        global $pdo;
        $this->model = new User($pdo);
    }

    public function registration() {
        $input = file_get_contents('php://input');
        $userdata = json_decode($input, true);

        $this->model->create($userdata);
    }

    public function login() {
        $input = file_get_contents('php://input');
        $userdata = json_decode($input, true);

        $this->model->login($userdata);
    }

    public function autologin() {
        $jwt = getBearerToken();

        if (validateJWT($jwt)) {
            $this->model->autologin($jwt);
        } else echo json_encode([ 'code' => 400, 'message' => 'Invalid jwt' ]);
    }

    public function getUsername() {
        $user_id = $_GET['user_id'];
        $this->model->getUsername($user_id);
    }
}
