<?php

require_once './env.php';

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Return user_id from JWT
function getUserIdFromJWT($jwt, $secretKey) {
    try {
        $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));
        $decoded_array = (array) $decoded;

        if (isset($decoded_array['user_id'])) {
            return $decoded_array['user_id'];
        } else {
            error_log('user_id not found in JWT');
            return null;
        }
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

    public function create($data, $jwt) {
        $correct_fields = isset($data['title']);

        if (!$correct_fields) {
            http_response_code(400);
            $response = [ 'message' => 'Invalid task data' ];
        } else {
            $author_id = getUserIdFromJWT($jwt, $this->secretKey);
            $title = strip_tags($data['title']);
            $description = strip_tags($data['description']);
            $completed = 0; // false by default

            # Inserting task in db
            $stmt = $this->pdo->prepare('INSERT INTO tasks (author_id, title, description, completed) VALUES (?, ?, ?, ?)');
            try {
                $result = $stmt->execute([$author_id, $title, $description, $completed]);
                http_response_code(200);
                $response = ['message' => 'Task inserted successfully'];
            } catch (PDOException $e) {
                http_response_code(500);
                $response = [  'message' => 'Database error: ' . $e->getMessage() ];
            }
       }

        echo json_encode($response);
    }

    public function get_tasks($jwt) {
        if (is_null($jwt)) {
            http_response_code(400);
            $response = [ 'message' => 'JWT is null!'];
        } else {
            $author_id = getUserIdFromJWT($jwt, $this->secretKey);
    
            if (is_null($author_id)) {
                http_response_code(401);
                $response = [ 'message' => 'Invalid JWT: User ID is null!' ];
            } else {
                $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE author_id = ?;');
                
                try {
                    $stmt->execute([$author_id]);
                    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                    if ($tasks) {
                        http_response_code(200);
                        $response = ['message' => 'Tasks fetched successfully', 'tasks' => $tasks
                        ];
                    } else {
                        http_response_code(200); 
                        $response = ['message' => 'No tasks found for this user.', 'tasks' => null ];
                    }
                } catch (PDOException $e) {
                    http_response_code(500); 
                    $response = ['message' => 'Database error: ' . $e->getMessage() ];
                }
            }
        }
    
        echo json_encode($response);
    }
    

    public function update_complete($data) {
        $correct_fields = isset($data['task_id'], $data['complete']);
        if (!$correct_fields) {
            http_response_code(400); 
            $response = [  'message' => "Invalid data: 'completed' must be setted!" ];
        } else {
            $completed = $data['complete'];
            $task_id = $data['task_id'];

            $stmt = $this->pdo->prepare('UPDATE tasks SET completed=? WHERE id=?;');
            try {
                $result = $stmt->execute([$completed, $task_id]);
                
                http_response_code(200); 
                $response = [ 'message' => "Task updated!", 'completed' => $completed];
            } catch (PDOException $e) {
                http_response_code(500); 
                $response = [ 'message' => 'Database error: ' . $e->getMessage() ];
            }
       }

        echo json_encode($response);
    }

    public function edit_task($data) {
        $correct_fields = isset($data['task_id'], $data['title'], $data['description']);
        if (!$correct_fields) {
            http_response_code(400); 
            $response = [ 'message' => "Invalid data: 'title', 'description' and 'task_id' must be setted!" ];
        } else {
            $task_id = $data['task_id'];
            $title = strip_tags($data['title']);
            $desc = strip_tags($data['description']);

            $stmt = $this->pdo->prepare('UPDATE tasks SET title=?, description=? WHERE id=?;');
            try {
                $result = $stmt->execute([$title, $desc, $task_id]);
                
                http_response_code(200); 
                $response = ['message' => "Task edited!"];
            } catch (PDOException $e) {
                http_response_code(500); 
                $response = [ 'message' => 'Database error: ' . $e->getMessage() ];
            }
       }

        echo json_encode($response);
    }
    
    public function delete($data) {
        $correct_fields = isset($data['task_id']);

        if (!$correct_fields) {
            http_response_code(400); 
            $response = [ 'message' => "Invalid data: 'task_id' must be setted!" ];
        } else {
            $task_id = $data['task_id'];

            $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE id=?;');
            try {
                $result = $stmt->execute([$task_id]);
                http_response_code(200); 
                $response = [ 'status' => true, 'message' => "Task deleted!"];
            } catch (PDOException $e) {
                http_response_code(500); 
                $response = [ 'message' => 'Database error: ' . $e->getMessage() ];
            }
       }

        echo json_encode($response);
    }
}