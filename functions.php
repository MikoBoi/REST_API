<?php

function getUser($id, $connect){

    $user = mysqli_query($connect,"SELECT id, username, age FROM users WHERE id = '$id'");

    if (mysqli_num_rows($user) === 0) {
        http_response_code(404);
        $response = [
            "status"=> false,
            "message"=> "User not found"
        ];

        echo json_encode($response);

    }
    else {
        $user = mysqli_fetch_assoc($user);

        echo json_encode($user);
    }
}

function loginUser($data, $connect){

    $username = $data["username"];
    $password = $data["password"];

    $check = mysqli_query($connect,"SELECT id FROM users WHERE username = '$username' AND password = '$password'");
    if (mysqli_num_rows($check) === 0) {
        http_response_code(404);
        $response = [
            "status"=> false,
            "message"=> "Incorrect login or password"
        ];

        echo json_encode($response);

    }
    $user_id = mysqli_fetch_assoc($check)["id"];
    $token = bin2hex(random_bytes(8));
    mysqli_query($connect,"INSERT INTO `tokens`(`value`, `user_id`) VALUES ('$token','$user_id')");
    http_response_code(201);
    $response = [
        "status"=> true,
        "message"=> "Success logged in!",
        "token"=> $token
    ];

    echo json_encode($response);

}

function addUser($data, $connect){

    $username = $data["username"];
    $password = $data["password"];
    $age = $data["age"];

    $check = mysqli_query($connect,"SELECT id, username, age FROM users WHERE username = '$username'");

    if (mysqli_num_rows($check) === 1){
        http_response_code(409);
        $response = [
            "status"=> false,
            "message"=> "User is already exist"
        ];

        echo json_encode($response);
    }
    else {
        mysqli_query($connect,"INSERT INTO `users`(`username`, `password`, `age`) VALUES ('$username','$password','$age')");
        http_response_code(201);
        $response = [
            "status"=> true,
            "user_id"=> mysqli_insert_id($connect)
        ];

        echo json_encode($response);
    }
}

function updateUser($id, $data, $connect){

    $user = mysqli_query($connect,"SELECT username, password, age FROM users WHERE id = '$id'");

    if (mysqli_num_rows($user) === 0) {
        http_response_code(404);
        $response = [
            "status"=> false,
            "message"=> "User with ID: $id does not exist"
        ];

        echo json_encode($response);

    }
    else {
        $targetUser = mysqli_fetch_assoc($user);

        $password = $targetUser["password"];
        $age = $targetUser["age"];

        if (isset($data['password'])) {
            $password = $data['password'];
        }
        if (isset($data['age'])) {
            $age = $data['age'];
        }

        mysqli_query($connect,"UPDATE `users` SET `password`='$password',`age`='$age' WHERE id = '$id'");
        http_response_code(200);
        $response = [
            "status"=> true,
            "message"=> "User information has been updated"
        ];

        echo json_encode($response);
    }
}

function deleteUser($id, $connect){
    
    $user = mysqli_query($connect,"SELECT id, username FROM users WHERE id = '$id'");

    if (mysqli_num_rows($user) === 0) {
        http_response_code(404);
        $response = [
            "status"=> false,
            "message"=> "User with ID: $id does not exist"
        ];

        echo json_encode($response);

    }
    else {
        mysqli_query($connect,"DELETE FROM `users` WHERE id = '$id'");
        http_response_code(200);
        $response = [
            "status"=> true,
            "message"=> "User with ID: $id has been deleted"
        ];

        echo json_encode($response);
    }


}