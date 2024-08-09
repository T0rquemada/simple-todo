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

        if ($method === 'POST' && $uri === '/tasks/create_task') {
            $this->middleware->handle($data['jwt']);
            $this->controller->create($data);
        } else if ($method === 'GET' && strpos($uri, '/tasks/get_tasks') === 0) {
            $jwt = $_GET['jwt'];
            $this->middleware->handle($jwt);
            $this->controller->get_tasks($jwt);
        } else if ($method === 'PUT' && $uri === '/tasks/update_task') {
            $this->middleware->handle($data['jwt']);
            $this->controller->update_task();
        } else {
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['code' => 404, 'message' => 'Route not found']);
        }
    }
}