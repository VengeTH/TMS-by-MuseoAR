<?php
/* usage:
	$db = new db();
	$db->functionName($email, $password);
	you can add your own functions here. to have a centralized database connection.
*/
class db {
	public $conn;
	public function __construct() {
		$host = "localhost"; // Database host
		$db = "TaskManagementDB"; // Database name
		$user = "root"; // Database username
		$pass = ""; // Database password
		$this->conn = new mysqli($host, $user, $pass, $db);
		if ($this->conn->connect_error) {
			die("Connection failed: " . $this->conn->connect_error);
		}
	}
	public function getConnection() {
		return $this->conn;
	}
	public function addUser($first_name, $last_name, $email, $password) {
		$hashed_password = password_hash($password, PASSWORD_DEFAULT);
		$stmt = $this->conn->prepare(
			"INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)",
		);
		$stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);
		$isSuccess = $stmt->execute();
		$stmt->close();
		return $isSuccess;
	}
	public function getUser($email) {
		$stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
		$stmt->bind_param("s", $email);
		if ($stmt->execute()) {
			$result = $stmt->get_result();
			if ($result->num_rows > 0) {
				// assuming an email is unique. use bind_result if not.
				$user = $result->fetch_assoc();
				$stmt->close();
				return $user;
			}
		}
		$stmt->close();
		return null;
	}
	public function updateUserImage($id, $google_picture) {
		$update_stmt = $this->conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
		$update_stmt->bind_param("si", $google_picture, $id);
		$isSuccess = $update_stmt->execute();
		$update_stmt->close();
		return $isSuccess;
	}
	public function updatePassword($id, $password) {
		$hashed_password = password_hash($password, PASSWORD_DEFAULT);
		$update_stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
		$update_stmt->bind_param("si", $hashed_password, $id);
		if ($update_stmt->execute()) {
			$update_stmt->close();
			return true;
		} else {
			$update_stmt->close();
			return false;
		}
	}
	public function loginUser($email, $password) {
		$stmt = $this->conn->prepare("SELECT id, first_name, password FROM users WHERE email = ?");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows > 0) {
			$stmt->bind_result($user_id, $first_name, $hashed_password);
			$stmt->fetch();
			if (password_verify($password, $hashed_password)) {
				$_SESSION["user_id"] = $user_id;
				$_SESSION["first_name"] = $first_name;
				return "";
			} else {
				$login_error = "Invalid password.";
			}
		} else {
			$login_error = "No user found with that email.";
		}
		return $login_error;
		$stmt->close();
	}

	// you can set a cache here to avoid multiple queries
	public function checkUser($id, $first_name) {
		$stmt = $this->conn->prepare("SELECT id FROM users WHERE id = ? AND first_name = ?");
		$stmt->bind_param("is", $id, $first_name);
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows > 0) {
			$stmt->close();
			return true;
		}
		$stmt->close();
		return false;
	}

	public function getUserById($id) {
		$stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$result = $stmt->get_result();
		$user = $result->fetch_assoc();
		$stmt->close();
		return $user;
	}
	public function getLastInsertId() {
		return $this->conn->insert_id;
	}
	public function __destruct() {
		$this->conn->close();
	}

	public function checkPassword($id) {
		$stmt = $this->conn->prepare("SELECT password FROM users WHERE id = ?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows > 0) {
			$stmt->close();
			return true;
		}
		$stmt->close();
		return false;
	}

	public function addTask($userId, $title, $details, $finishDate, $priority) {
		$stmt = $this->conn->prepare("INSERT INTO tasks (user_id, title, details, finish_date, priority) VALUES (?, ?, ?, ?, ?)");
		$stmt->bind_param("issss", $userId, $title, $details, $finishDate, $priority);
		$isSuccess = $stmt->execute();
		$stmt->close();
		return $isSuccess;
	}
}
?>