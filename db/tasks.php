<?php
class Task{
    private $conn;
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "TaskManagementDB";
    private $tableName = "tasks";
    public function __construct(){
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
   }
    public function addTask($title, $details, $finishDate, $priority, $userId, $parentTaskId = null){
        $stmt = $this->conn->prepare("INSERT INTO $this->tableName (user_id, title, details, finish_date, priority, parent_task_id) VALUES (?, ?, ?, ?, ?, ?)");
        if ($priority === "high") {
            $priority = 3;
        } else if ($priority === "medium") {
            $priority = 2;
        } else if ($priority === "low") {
            $priority = 1;
        } else if (is_int($priority)) {
            $priority = max(1, min(3, $priority));
        } else {
            $priority = 1;
        }
        $stmt->bind_param("isssii", $userId, $title, $details, $finishDate, $priority, $parentTaskId);
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
        $stmt = $this->conn->prepare("UPDATE $this->tableName SET title = ?, details = ?, finish_date = ?, priority = ? WHERE id = ? AND user_id = ?");
        if ($priority === "high") {
            $priority = 3;
        } else if ($priority === "medium") {
            $priority = 2;
        } else {
            $priority = 1;
        }
        $stmt->bind_param("sssiii", $title, $details, $finishDate, $priority, $taskId, $userId);
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
    public function deleteTasks($taskIds, $userId) {
        $placeholders = implode(',', array_fill(0, count($taskIds), '?'));
        $types = str_repeat('i', count($taskIds)) . 'i';
        $stmt = $this->conn->prepare("DELETE FROM $this->tableName WHERE id IN ($placeholders) AND user_id = ?");
        $params = array_merge($taskIds, [$userId]);
        $stmt->bind_param($types, ...$params);
        $isSuccess = $stmt->execute();
        $stmt->close();
        return $isSuccess;
    }
    public function getTaskCount($userId) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as task_count FROM $this->tableName WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()["task_count"];
        $stmt->close();
        return $count;
    }

    public function getCompletedTodayCount($userId, $timezone = "Asia/Manila") {
        $tz = new DateTimeZone($timezone);
        $today = (new DateTime("now", $tz))->format("Y-m-d");
        $start = $today . " 00:00:00";
        $end = $today . " 23:59:59";

        $stmt = $this->conn->prepare("SELECT COUNT(*) AS c FROM $this->tableName WHERE user_id = ? AND is_completed = 1 AND completed_at BETWEEN ? AND ?");
        if ($stmt === false) {
            return 0;
        }
        $stmt->bind_param("iss", $userId, $start, $end);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return isset($row["c"]) ? (int) $row["c"] : 0;
    }

    public function getCompletionStreakDays($userId, $timezone = "Asia/Manila") {
        $tz = new DateTimeZone($timezone);
        $today = new DateTimeImmutable("now", $tz);

        $stmt = $this->conn->prepare("SELECT DATE(completed_at) AS d FROM $this->tableName WHERE user_id = ? AND is_completed = 1 AND completed_at IS NOT NULL GROUP BY DATE(completed_at) ORDER BY d DESC LIMIT 60");
        if ($stmt === false) {
            return 0;
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $dates = [];
        while ($row = $result->fetch_assoc()) {
            if (isset($row["d"]) && is_string($row["d"])) {
                $dates[] = $row["d"];
            }
        }
        $stmt->close();

        if (count($dates) === 0) {
            return 0;
        }

        $streak = 0;
        $cursor = $today;

        // * Allow streak counting starting today; if no completion today, streak starts from yesterday.
        if (!in_array($cursor->format("Y-m-d"), $dates, true)) {
            $cursor = $cursor->modify("-1 day");
        }

        while (true) {
            $day = $cursor->format("Y-m-d");
            if (!in_array($day, $dates, true)) {
                break;
            }
            $streak += 1;
            $cursor = $cursor->modify("-1 day");
            if ($streak >= 60) {
                break;
            }
        }

        return $streak;
    }
    public function updateTaskFinishDate($userId, $taskId, $finishDate) {
        $stmt = $this->conn->prepare("UPDATE $this->tableName SET finish_date = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $finishDate, $taskId, $userId);
        $isSuccess = $stmt->execute();
        $stmt->close();
        return $isSuccess;
    }
    public function getLastInsertId() {
        return $this->conn->insert_id;
    }
    public function getConnection() {
        return $this->conn;
    }
    public function __destruct(){
        $this->conn->close();
    }
}
?>