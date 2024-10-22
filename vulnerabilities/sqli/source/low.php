<?php

if (isset($_REQUEST['Submit'])) {
    // Get input
    $id = $_REQUEST['id'];

    switch ($_DVWA['SQLI_DB']) {
        case MYSQL:
            // Prepare the SQL query using prepared statements
            $stmt = $GLOBALS["___mysqli_ston"]->prepare("SELECT first_name, last_name FROM users WHERE user_id = ?");
            
            if (!$stmt) {
                die('<pre>Error preparing statement: ' . $GLOBALS["___mysqli_ston"]->error . '</pre>');
            }

            // Bind the parameter
            $stmt->bind_param('i', $id);  // 'i' indicates the parameter type is an integer

            // Execute the query
            $stmt->execute();

            // Get the result
            $result = $stmt->get_result();

            // Fetch the result
            while ($row = $result->fetch_assoc()) {
                // Get values
                $first = htmlspecialchars($row['first_name'], ENT_QUOTES, 'UTF-8');
                $last  = htmlspecialchars($row['last_name'], ENT_QUOTES, 'UTF-8');

                // Feedback for end user
                $html .= "<pre>ID: " . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . "<br />First name: {$first}<br />Surname: {$last}</pre>";
            }

            // Close the statement and connection
            $stmt->close();
            mysqli_close($GLOBALS["___mysqli_ston"]);
            break;

        case SQLITE:
            global $sqlite_db_connection;

            // Prepare the SQL query using SQLite3 prepared statements
            $query = "SELECT first_name, last_name FROM users WHERE user_id = :id";
            $stmt = $sqlite_db_connection->prepare($query);

            if (!$stmt) {
                echo 'Error preparing SQLite statement: ' . $sqlite_db_connection->lastErrorMsg();
                exit();
            }

            // Bind the parameter
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);

            // Execute the query
            $results = $stmt->execute();

            // Fetch the result
            if ($results) {
                while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                    // Get values
                    $first = htmlspecialchars($row['first_name'], ENT_QUOTES, 'UTF-8');
                    $last  = htmlspecialchars($row['last_name'], ENT_QUOTES, 'UTF-8');

                    // Feedback for end user
                    $html .= "<pre>ID: " . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . "<br />First name: {$first}<br />Surname: {$last}</pre>";
                }
            } else {
                echo "Error in fetch: " . $sqlite_db_connection->lastErrorMsg();
            }

            // Close the statement and connection
            $stmt->close();
            break;
    }
}
?>
