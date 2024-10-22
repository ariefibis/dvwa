<?php
define('DVWA_WEB_PAGE_TO_ROOT', '../../');
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

// Prepare the SQL statement
$stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], "UPDATE users SET first_name = ?, last_name = ? WHERE user_id = ?");
if ($stmt === false) {
    die('<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '</pre>');
}

// Bind parameters
mysqli_stmt_bind_param($stmt, 'ssi', $data->first_name, $data->surname, $data->id);

// Execute the statement
mysqli_stmt_execute($stmt);

// Check for errors
if (mysqli_stmt_affected_rows($stmt) === -1) {
    $result = array(
        "result" => "fail",
        "error" => 'Failed to update user details'
    );
    echo json_encode($result);
    exit;
}

// Close the statement
mysqli_stmt_close($stmt);

// Close the connection
((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);

print json_encode(array("result" => "ok"));
exit;
?>