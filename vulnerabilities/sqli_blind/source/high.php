<?php

if (isset($_COOKIE['id'])) {
    // Get input
    $id = $_COOKIE['id'];
    $exists = false;

    switch ($_DVWA['SQLI_DB']) {
        case MYSQL:
            // Prepare the SQL statement
            $stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], "SELECT first_name, last_name FROM users WHERE user_id = ? LIMIT 1");
            if ($stmt === false) {
                die('<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '</pre>');
            }

            // Bind parameters
            mysqli_stmt_bind_param($stmt, 's', $id);

            // Execute the statement
            mysqli_stmt_execute($stmt);

            // Get the result
            $result = mysqli_stmt_get_result($stmt);

            // Check if user exists
            $exists = ($result && mysqli_num_rows($result) > 0);

            // Close the statement
            mysqli_stmt_close($stmt);

            // Close the connection
            ((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
            break;

        case SQLITE:
            global $sqlite_db_connection;

            // Prepare the SQL statement
            $query = "SELECT first_name, last_name FROM users WHERE user_id = :id LIMIT 1";
            $stmt = $sqlite_db_connection->prepare($query);

            // Bind parameters
            $stmt->bindValue(':id', $id, SQLITE3_TEXT);

            // Execute the statement
            $results = $stmt->execute();

            // Check if user exists
            $row = $results->fetchArray();
            $exists = $row !== false;

            // Close the statement
            $stmt->close();
            break;
    }

    if ($exists) {
        // Feedback for end user
        $html .= '<pre>User ID exists in the database.</pre>';
    } else {
        // Might sleep a random amount
        if (rand(0, 5) == 3) {
            sleep(rand(2, 4));
        }

        // User wasn't found, so the page wasn't!
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');

        // Feedback for end user
        $html .= '<pre>User ID is MISSING from the database.</pre>';
    }
}

?>