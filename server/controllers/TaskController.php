<?php
/** @var PDO $pdo */
require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/../models/Task.php';

class TaskController {
    private $model;

    public function __construct() {
        global $pdo;
        $this->model = new Task($pdo);
    }

    public function create($data) {
        $this->model->create($data);
    }

    public function get_tasks($jwt) {
        $this->model->get_tasks($jwt);
    }

    public function update_task($data) {
        $this->model->update($data);
    }
}