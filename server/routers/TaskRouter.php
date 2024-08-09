<?php

require_once __DIR__ . '/../controllers/TaskController.php';

class TaskRouter {
    private $controller;

    public function __construct() {
        $this->controller = new TaskController();
    }

    public function route() {
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'POST' && $uri === '/tasks/create_task') {
            $this->controller->create();
        } else if ($method === 'GET' && strpos($uri, '/tasks/get_tasks') === 0) {
            $this->controller->get_tasks();
        } else if ($method === 'PUT' && $uri === '/tasks/update_task') {
            $this->controller->update_task();
        } else {
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['code' => 404, 'message' => 'Route not found']);
        }
    }
}