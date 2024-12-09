<?php

class db {
	private $conn;
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
	public function addUser($first_name, $last_name, $email, $image) {
		$stmt = $this->conn->prepare(
			"INSERT INTO users (first_name, last_name, email, profile_picture, profile_picture_source) VALUES (?, ?, ?, ?, 'google')",
		);
		$stmt->bind_param("ssss", $first_name, $last_name, $email, $google_picture);
		// execute and check if the query was successful
		if ($stmt->execute()) {
			$userId = $stmt->id;
			$stmt->close();
			return $userId;
		} else {
			$stmt->close();
			return false;
		}
	}
	public function getUser($email) {
		$stmt = $this->conn->prepare(
			"SELECT id, first_name, profile_picture, profile_picture_source FROM users WHERE email = ?",
		);
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
	public function updateUserImage($id, $image) {
		$update_stmt = $this->conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
		$update_stmt->bind_param("si", $google_picture, $user["id"]);
		if ($update_stmt->execute()) {
			$update_stmt->close();
			return true;
		} else {
			$update_stmt->close();
			return false;
		}
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
	public function __destruct() {
		$this->conn->close();
	}
}

?>
