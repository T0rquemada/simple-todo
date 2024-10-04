<?php

require_once __DIR__ . '/../controllers/UserController.php';

class UserRouter {
    private $controller;

    public function __construct() {
        $this->controller = new UserController();
    }

    public function route() {
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];

        $input = file_get_contents('php://input');
        $userdata = json_decode($input, true);

        if ($method === 'POST' && $uri === '/users/registration') {
            $this->controller->registration($userdata);
        } else if ($method === 'POST' && $uri === '/users/login') {
            $this->controller->login($userdata);
        } else if ($method === 'GET' && $uri === '/users/autologin') {
            $this->controller->autologin();
        } else if ($method === 'GET' && strpos($uri, '/users/get_username') === 0) {
            $this->controller->get_username();
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Route not found']);
        }
    }
}