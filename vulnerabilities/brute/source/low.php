<?php

if (isset($_GET['Login'])) {
    // Get username and password from request
    $user = $_GET['username'];
    $pass = $_GET['password'];

    // Check the database using prepared statements
    $stmt = $GLOBALS["___mysqli_ston"]->prepare("SELECT * FROM `users` WHERE user = ?");

    if (!$stmt) {
        die('<pre>Error preparing statement: ' . $GLOBALS["___mysqli_ston"]->error . '</pre>');
    }

    // Bind the username parameter
    $stmt->bind_param('s', $user);
    
    // Execute the query
    $stmt->execute();
    
    // Get the result
    $result = $stmt->get_result();

    // Check if a user with the provided username exists
    if ($result && mysqli_num_rows($result) == 1) {
        // Fetch the user details
        $row = $result->fetch_assoc();
        
        // Verify the password using password_verify
        if (password_verify($pass, $row['password'])) {
            // Get user's avatar
            $avatar = $row["avatar"];

            // Login successful
            $html .= "<p>Welcome to the password protected area {$user}</p>";
            $html .= "<img src=\"{$avatar}\" />";
        } else {
            // Password incorrect
            $html .= "<pre><br />Username and/or password incorrect.</pre>";
        }
    } else {
        // No user found or multiple results (which shouldn't happen)
        $html .= "<pre><br />Username and/or password incorrect.</pre>";
    }

    // Close the statement and connection
    $stmt->close();
    mysqli_close($GLOBALS["___mysqli_ston"]);
}

?>
