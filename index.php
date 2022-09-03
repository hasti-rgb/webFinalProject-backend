<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
include 'DbConnect.php';
$objDb = new DbConnect;
$conn = $objDb->connect();

$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case "GET":
        $sql = "SELECT * FROM tasks";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if (isset($path[3]) && is_numeric($path[3])) {
            $sql .= " WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $path[3]);
            $stmt->execute();
            $tasks = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode($tasks);
        break;

    case "POST":
        $task = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO tasks(id,title,description,status) VALUES (null,:title,:description,'Todo')";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $task->title);
        $stmt->bindParam(':description', $task->description);
        // $stmt->bindParam(':status', $task->status);
        if ($stmt->execute()) {
            $reponse = ['status' => 1, 'message' => 'Record created successfully.'];
        } else {
            $reponse = ['status' => 0, 'message' => 'Failed to create record.'];
        }
        break;
        // $created_at = date('Y-m-d');
    case "DELETE":
        $sql = "DELETE * FROM tasks WHERE id = :id";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $path[3]);
        if ($stmt->execute()) {
            $reponse = ['status' => 1, 'message' => 'Record deleted successfully.'];
        } else {
            $reponse = ['status' => 0, 'message' => 'Failed to delete record.'];
        }
        break;
}
