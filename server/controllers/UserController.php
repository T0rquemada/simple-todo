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

    public function registration($userdata) {
        if (empty($userdata['email']) || empty($userdata['password'] || empty($userdata['password']))) {
            http_response_code(400);
            return json_encode(['message' => 'Incorrect userdata while registration!']);
        } else {
            $this->model->create($userdata);
        }
    }

    public function login($userdata) {
        if (empty($userdata['email']) || empty($userdata['password'])) {
            http_response_code(400);
            return json_encode(['message' => 'Incorrect userdata while login!']);
        } else {
            $this->model->login($userdata);
        }
    }

    public function autologin() {
        $jwt = getBearerToken();

        if (empty($jwt)) {
            http_response_code(400);
            return json_encode(['message' => 'JWT not provided!']);
        }

        if (validateJWT($jwt)) {
            $this->model->autologin($jwt);
        } else echo json_encode([ 'code' => 400, 'message' => 'Invalid jwt' ]);
    }

    public function get_username() {
        $user_id = $_GET['user_id'];
        $this->model->get_username($user_id);
    }
}
