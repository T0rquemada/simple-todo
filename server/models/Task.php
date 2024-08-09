<?php

require_once './env.php';

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Return user_id from JWT
function getUserIdFromJWT($jwt, $secretKey) {
    try {
        $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));
        $decoded_array = (array) $decoded; // Convert to array
        $user_id = $decoded_array['user_id'];
        return $user_id;
    } catch (Exception $e) {
        error_log('JWT decode error: ' . $e->getMessage());
        return null;
    }
}

class Task {
    private $pdo;
    private $secretKey;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->secretKey = $_ENV['JWT'];
    }

    public function create($data) {
        $correct_fields = isset($data['jwt'], $data['title'], $data['description']);
        if (!$correct_fields) {
            $response = [ 'code' => 400, 'message' => 'Invalid task data' ];
        } else {
            # Inserting task in db
            $author_id = getUserIdFromJWT($data['jwt'], $this->secretKey);
            $title = $data['title'];
            $description = $data['description'];
            $completed = 0; // false

            $stmt = $this->pdo->prepare('INSERT INTO tasks (author_id, title, description, completed) VALUES (?, ?, ?, ?)');
            try {
                $result = $stmt->execute([$author_id, $title, $description, $completed]);
                $response = [ 'code' => 200, 'message' => 'Task inserted successfully'];
            } catch (PDOException $e) {
                $response = [ 'code' => 500, 'message' => 'Database error: ' . $e->getMessage() ];
            }
       }

        echo json_encode($response);
    }

    public function get_tasks($jwt) {
        if (is_null($jwt)) {
            $response = [ 'code' => 400, 'message' => 'jwt is null!' ];
        } else {
            $author_id = getUserIdFromJWT($jwt, $this->secretKey);

            $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE author_id=?;');
            try {
                $stmt->execute([$author_id]);
                $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $response = [ 'code' => 200, 'message' => 'Task fetched successfully', 'tasks' => $tasks];
            } catch (PDOException $e) {
                $response = [ 'code' => 500, 'message' => 'Database error: ' . $e->getMessage() ];
            }
        }

        echo json_encode($response);
    }

    public function update($data) {
        $correct_fields = isset($data['task_id'], $data['complete']);
        if (!$correct_fields) {
            $response = [ 'code' => 400, 'message' => "Invalid data: 'completed' must be setted!" ];
            echo json_encode($response);
        } else {
            $completed = $data['complete'];
            $task_id = $data['task_id'];

            $stmt = $this->pdo->prepare('UPDATE tasks SET completed=? WHERE id=?;');
            try {
                $result = $stmt->execute([$completed, $task_id]);
                $response = [ 'code' => 200, 'message' => 'Task inserted successfully'];
            } catch (PDOException $e) {
                $response = [ 'code' => 500, 'message' => 'Database error: ' . $e->getMessage() ];
            }
            $response = ['code' => 200, 'message' => "Task updated!", 'completed' => $completed];
       }

        echo json_encode($response);
    }
}