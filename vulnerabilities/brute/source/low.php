<?php

if (isset($_GET['Login'])) {
    // Get username
    $user = $_GET['username'];

    // Get password
    $pass = $_GET['password'];
    $pass = md5($pass);

    // Prepare the SQL statement
    $stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], "SELECT * FROM `users` WHERE user = ? AND password = ?");
    if ($stmt === false) {
        die('<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '</pre>');
    }

    // Bind parameters
    mysqli_stmt_bind_param($stmt, 'ss', $user, $pass);

    // Execute the statement
    mysqli_stmt_execute($stmt);

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) == 1) {
        // Get user's details
        $row = mysqli_fetch_assoc($result);
        $avatar = $row["avatar"];

        // Login successful
        $html .= "<p>Welcome to the password protected area {$user}</p>";
        $html .= "<img src=\"{$avatar}\" />";
    } else {
        // Login failed
        $html .= "<pre><br />Username and/or password incorrect.</pre>";
    }

    // Close the statement
    mysqli_stmt_close($stmt);

    // Close the connection
    ((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
}

?>