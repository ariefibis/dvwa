<?php
define( 'DVWA_WEB_PAGE_TO_ROOT', '../../' );
require_once DVWA_WEB_PAGE_TO_ROOT . 'dvwa/includes/dvwaPage.inc.php';

dvwaDatabaseConnect();

/*
On impossible only the admin is allowed to retrieve the data.
*/
if (dvwaSecurityLevelGet() == "impossible" && dvwaCurrentUser() != "admin") {
	print json_encode(array("result" => "fail", "error" => "Access denied"));
	exit;
}

if ($_SERVER['REQUEST_METHOD'] != "POST") {
	$result = array(
		"result" => "fail",
		"error" => "Only POST requests are accepted"
	);
	echo json_encode($result);
	exit;
}

try {
	$json = file_get_contents('php://input');
	$data = json_decode($json);
	if (is_null($data)) {
		$result = array(
			"result" => "fail",
			"error" => 'Invalid format, expecting "{id: {user ID}, first_name: "{first name}", surname: "{surname}"}'
		);
		echo json_encode($result);
		exit;
	}
} catch (Exception $e) {
	$result = array(
		"result" => "fail",
		"error" => 'Invalid format, expecting "{id: {user ID}, first_name: "{first name}", surname: "{surname}"}'
	);
	echo json_encode($result);
	exit;
}

// Prepare and sanitize the input data
$user_id = $data->id;
$first_name = $data->first_name;
$surname = $data->surname;

// Prepare the SQL query using prepared statements
$stmt = $GLOBALS["___mysqli_ston"]->prepare("UPDATE users SET first_name = ?, last_name = ? WHERE user_id = ?");

if (!$stmt) {
	die(json_encode(array("result" => "fail", "error" => $GLOBALS["___mysqli_ston"]->error)));
}

// Bind the parameters to the statement
$stmt->bind_param("ssi", $first_name, $surname, $user_id); // "ssi" stands for string, string, integer

// Execute the query
if ($stmt->execute()) {
	print json_encode(array("result" => "ok"));
} else {
	print json_encode(array("result" => "fail", "error" => $stmt->error));
}

// Close the statement and connection
$stmt->close();
mysqli_close($GLOBALS["___mysqli_ston"]);

exit;
?>
