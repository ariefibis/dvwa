<?php

if (isset($_REQUEST['Submit'])) {
    // Get input
    $id = $_REQUEST['id'];

    switch ($_DVWA['SQLI_DB']) {
        case MYSQL:
            // Prepare the SQL statement
            $stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], "SELECT first_name, last_name FROM users WHERE user_id = ?");
            if ($stmt === false) {
                die('<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '</pre>');
            }

            // Bind parameters
            mysqli_stmt_bind_param($stmt, 's', $id);

            // Execute the statement
            mysqli_stmt_execute($stmt);

            // Get the result
            $result = mysqli_stmt_get_result($stmt);

            // Get results
            while ($row = mysqli_fetch_assoc($result)) {
                // Get values
                $first = $row["first_name"];
                $last  = $row["last_name"];

                // Feedback for end user
                $html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
            }

            // Close the statement
            mysqli_stmt_close($stmt);

            // Close the connection
            ((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
            break;

        case SQLITE:
            global $sqlite_db_connection;

            // Prepare the SQL statement
            $query = "SELECT first_name, last_name FROM users WHERE user_id = :id";
            $stmt = $sqlite_db_connection->prepare($query);

            // Bind parameters
            $stmt->bindValue(':id', $id, SQLITE3_TEXT);

            // Execute the statement
            $results = $stmt->execute();

            // Get results
            if ($results) {
                while ($row = $results->fetchArray()) {
                    // Get values
                    $first = $row["first_name"];
                    $last  = $row["last_name"];

                    // Feedback for end user
                    $html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
                }
            } else {
                echo "Error in fetch " . $sqlite_db_connection->lastErrorMsg();
            }

            // Close the statement
            $stmt->close();
            break;
    }
}

?>