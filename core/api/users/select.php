<?php

require("../../db/config.php");

$users = mysqli_query($conn, "SELECT * FROM users");

getUsers($conn, $users);

function getUsers($conn, $users)
{
    // Check if query was successful
    if (!$users) {
        die('Query failed: ' . mysqli_error($conn));
    } else {
        $userArray = mysqli_fetch_all($users, MYSQLI_ASSOC);
        foreach ($userArray as $user) {
            echo json_encode($user);
        }
    }
}