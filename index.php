<?php

require 'connect.php';
require 'functions.php';

header('Content-Type: json/application');

$q = $_GET['q'];
$params = explode('/', $q);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
	case 'POST':
		if ($params[0] === 'register') {
			addUser($_POST, $connect);
		}

		if ($params[0] === 'login') {
			loginUser($_POST, $connect);
		}

		else { http_response_code(404); }
        break;

    case 'GET':
		if ($params[0] === 'profile') {
			$token = explode(" ", getallheaders()['Authorization'])[1];
			$checkToken = mysqli_query($connect,"SELECT user_id FROM tokens WHERE value = '$token'");
			if (mysqli_num_rows($checkToken) === 0) {
				http_response_code(404);
				$response = [
					"status"=> false,
					"message"=> "Invalid token"
				];
				echo json_encode($response);
			}
			else {			
				$fromToken = mysqli_fetch_assoc($checkToken)["user_id"];
				getUser($fromToken, $connect);
			}
		}
		else { http_response_code(404); }
        break;

	case 'PATCH':
		if ($params[0] === 'edit') {
			$data = file_get_contents('php://input');
			$data = json_decode($data, true);

			$token = explode(" ", getallheaders()['Authorization'])[1];
			$checkToken = mysqli_query($connect,"SELECT user_id FROM tokens WHERE value = '$token'");
			if (mysqli_num_rows($checkToken) === 0) {
				http_response_code(404);
				$response = [
					"status"=> false,
					"message"=> "Invalid token"
				];
				echo json_encode($response);
			}
			else {			
				$fromToken = mysqli_fetch_assoc($checkToken)["user_id"];
				updateUser($fromToken, $data, $connect);
			}
		}
		else { http_response_code(404); }
		break;

	case 'DELETE':
		if ($params[0] === 'delete') {
			$token = explode(" ", getallheaders()['Authorization'])[1];
			$checkToken = mysqli_query($connect,"SELECT user_id FROM tokens WHERE value = '$token'");
			if (mysqli_num_rows($checkToken) === 0) {
				http_response_code(404);
				$response = [
					"status"=> false,
					"message"=> "Invalid token"
				];
				echo json_encode($response);
			}
			else {			
				$fromToken = mysqli_fetch_assoc($checkToken)["user_id"];
				deleteUser($fromToken, $connect);
			}
		}
		else { http_response_code(404); }
		break;

    default:
        echo json_encode(['message' => 'Метод не поддерживается']);
        break;
}
?>