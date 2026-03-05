<?php

/**
 * * Ensures a user is authenticated for JSON API endpoints.
 * * Starts the session if necessary and returns the user id.
 *
 * @return int
 */
function requireAuthJson(): int
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!isset($_SESSION["user_id"])) {
        header("Content-Type: application/json; charset=utf-8");
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Unauthorized.",
        ]);
        exit();
    }

    return (int) $_SESSION["user_id"];
}

?>
