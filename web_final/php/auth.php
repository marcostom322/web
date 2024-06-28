<?php
session_start();
include 'db.php';

function login($username, $password) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];  // Agregar user_id a la sesión
            $_SESSION['user_type'] = $user['user_type'];
            header("Location: ../dashboard.php");
            exit();
        } else {
            return "Invalid password.";
        }
    } else {
        return "Invalid username.";
    }
}

function register($username, $password, $name, $email) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO users (username, password, name, email) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $password, $name, $email);
    if ($stmt->execute()) {
        return "Registration successful. You can now login.";
    } else {
        return "Error: " . $stmt->error;
    }
}

function getUserDetails($username) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateUserDetails($username, $name, $email) {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE username=?");
    $stmt->bind_param("sss", $name, $email, $username);
    return $stmt->execute();
}
?>
