<?php
/** @var PDO $pdo */
require_once __DIR__ . '/../database/database.php';

require_once __DIR__ . '/../authMiddleware.php';
require_once __DIR__ . '/../controllers/TaskController.php';

class TaskRouter {
    private $controller;
    private $middleware;

    public function __construct() {
        global $pdo;
        $this->middleware = new AuthMiddleware($pdo);
        $this->controller = new TaskController();
    }

    public function route() {
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];

        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            http_response_code(400);
            echo json_encode(['message' => 'JWT from headers not provided!']);
            exit();
        }
        $token_field = $headers['Authorization'];
        $jwt = str_replace('Bearer ', '', $token_field);

        if ($method === 'POST' && $uri === '/tasks/create_task') {
            $this->middleware->handle($jwt);
            $this->controller->create($data, $jwt);
        } else if ($method === 'GET' && strpos($uri, '/tasks/get_tasks') === 0) {
            $this->middleware->handle($jwt);
            $this->controller->get_tasks($jwt);
        } else if ($method === 'PUT' && $uri === '/tasks/update_complete') {
            $this->middleware->handle($jwt);
            $this->controller->update_complete($data);
        } else if ($method === 'PUT' && $uri === '/tasks/edit_task') {
            $this->middleware->handle($jwt);
            $this->controller->edit_task($data);
        } else if ($method === 'DELETE' && $uri === '/tasks/delete_task') {
            $this->middleware->handle($jwt);
            $this->controller->delete($data);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Route not found']);
        }
    }
}