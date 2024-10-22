<?php

if (isset($_POST['Submit'])) {
    // Get input
    $target = trim($_REQUEST['ip']);

    // Use escapeshellarg to safely escape the input
    $target = escapeshellarg($target);

    // Determine OS and execute the ping command.
    if (stristr(php_uname('s'), 'Windows NT')) {
        // Windows
        $cmd = shell_exec('ping ' . $target);
    } else {
        // *nix
        $cmd = shell_exec('ping -c 4 ' . $target);
    }

    // Feedback for the end user
    $html .= "<pre>" . htmlspecialchars($cmd, ENT_QUOTES, 'UTF-8') . "</pre>";
}

?>
