<?php
class Task{
    private $conn;
    private $host = "localhost";
    private $username = "aisukurimu";
    private $password = "Password123";
    private $database = "TaskManagementDB";
    private $tableName = "tasks";
    public function __construct(){
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
   }
    public function addTask($title, $details, $finishDate, $priority, $userId){
        $stmt = $this->conn->prepare("INSERT INTO $this->tableName (user_id, title, details, finish_date, priority) VALUES (?, ?, ?, ?, ?)");
        if ($priority == "High") {
            $priority = 3;
        } else if ($priority == "Medium") {
            $priority = 2;
        } else {
            $priority = 1;
        }
        $stmt->bind_param("isssi", $userId, $title, $details, $finishDate, $priority);
        $isSuccess = $stmt->execute();
        $stmt->close();
        return $isSuccess;
    }
    public function getTasks($userId, $orderBasis = "priority", $order = "DESC"){
        $sql = "SELECT * FROM ". $this->tableName. " WHERE user_id = ? ORDER BY ?";
        if($order == "ASC"){
            $sql .= " ASC";
        }else{
            $sql .= " DESC";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $userId, $orderBasis);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            $tasks = array();
            while($row = $result->fetch_assoc()){
                $tasks[] = $row;
            }
            return $tasks;
        }
        return null;
    }
    public function getTask($taskId, $userId){
        $stmt = $this->conn->prepare("SELECT * FROM $this->tableName WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $taskId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $task = $result->fetch_assoc();
        $stmt->close();
        return $task;
    }
    public function updateTask( $userId, $taskId, $title, $details, $finishDate, $priority){
        $stmt = $this->conn->prepare("UPDATE $this->tableName SET title = ?, details = ?, finish_date = ?, priority = ? WHERE id = ?");
        if ($priority == "High") {
            $priority = 3;
        } else if ($priority == "Medium") {
            $priority = 2;
        } else {
            $priority = 1;
        }
        $stmt->bind_param("sssii", $title, $details, $finishDate, $priority, $taskId);
        $isSuccess = $stmt->execute();
        $stmt->close();
        return $isSuccess;
    }
    public function deleteTask($taskId, $userId){
        $stmt = $this->conn->prepare("DELETE FROM $this->tableName WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $taskId, $userId);
        $isSuccess = $stmt->execute();
        $stmt->close();
        return $isSuccess;
    }


    public function __destruct(){
        $this->conn->close();
    }
}
?>