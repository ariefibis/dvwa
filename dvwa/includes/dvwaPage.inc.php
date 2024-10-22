<?php

if (!defined('DVWA_WEB_PAGE_TO_ROOT')) {
    die('DVWA System error - WEB_PAGE_TO_ROOT undefined');
    exit;
}

if (!file_exists(DVWA_WEB_PAGE_TO_ROOT . 'config/config.inc.php')) {
    die('DVWA System error - config file not found. Copy config/config.inc.php.dist to config/config.inc.php and configure to your environment.');
}

// Include configs
require_once DVWA_WEB_PAGE_TO_ROOT . 'config/config.inc.php';

// Declare the $html variable
if (!isset($html)) {
    $html = "";
}

// Valid security levels
$security_levels = array('low', 'medium', 'high', 'impossible');
$cookie_security_level = isset($_COOKIE['security']) ? htmlspecialchars($_COOKIE['security'], ENT_QUOTES, 'UTF-8') : null; // Sanitize cookie input

if (!isset($cookie_security_level) || !in_array($cookie_security_level, $security_levels)) {
    // Set security cookie to impossible if no cookie exists
    if (in_array($_DVWA['default_security_level'], $security_levels)) {
        dvwaSecurityLevelSet($_DVWA['default_security_level']);
    } else {
        dvwaSecurityLevelSet('impossible');
    }
    // If the cookie wasn't set, then the session flags need updating.
    dvwa_start_session();
}

/*
 * This function is called after login and when you change the security level.
 * It gets the security level and sets the httponly and samesite cookie flags appropriately.
 *
 * To force an update of the cookie flags we need to update the session id,
 * just setting the flags and doing a session_start() does not change anything.
 * For this, session_id() or session_regenerate_id() can be used.
 * Both keep the existing session values, so nothing is lost,
 * it will just cause a new Set-Cookie header to be sent with the new right
 * flags and the new id (or the same one if we wish to keep it).
 */
function dvwa_start_session()
{
    // This will setup the session cookie based on the security level.
    $security_level = dvwaSecurityLevelGet();
    if ($security_level == 'impossible') {
        $httponly = true;
        $samesite = "Strict";
    } else {
        $httponly = false;
        $samesite = "";
    }

    $maxlifetime = 86400;
    $secure = false;
    $domain = parse_url($_SERVER['HTTP_HOST'], PHP_URL_HOST);

    // Close session if already active
    if (session_status() == PHP_SESSION_ACTIVE) {
        session_write_close();
    }

    session_set_cookie_params([
        'lifetime' => $maxlifetime,
        'path' => '/',
        'domain' => $domain,
        'secure' => $secure,
        'httponly' => $httponly,
        'samesite' => $samesite
    ]);

    // Regenerate or keep session ID based on security level
    if ($security_level == 'impossible') {
        session_start();
        session_regenerate_id(); // force a new ID to be generated
    } else {
        if (isset($_COOKIE[session_name()])) { // if a session id already exists
            session_id($_COOKIE[session_name()]); // we keep the same ID
        }
        session_start(); // otherwise, a new one will be generated here
    }
}

if (array_key_exists("Login", $_POST) && $_POST['Login'] == "Login") {
    dvwa_start_session();
} else {
    if (!session_id()) {
        session_start();
    }
}

if (!array_key_exists("default_locale", $_DVWA)) {
    $_DVWA['default_locale'] = "en";
}

dvwaLocaleSet($_DVWA['default_locale']);

// Function to sanitize user input before rendering in HTML
function sanitize_output($output)
{
    return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
}

// Sanitizing cookie output before rendering in HTML
$cookie_security_level_safe = sanitize_output(dvwaSecurityLevelGet());

/**
 * Functions like dvwaPageStartup, dvwaSecurityLevelGet, dvwaSecurityLevelSet, etc.,
 * should also make sure any data rendered to the page is sanitized properly.
 * Ensure that all values pulled from user input, especially from cookies and
 * the query string, are sanitized.
 */

// Example of outputting a sanitized cookie value in HTML
echo "<p>Current security level: {$cookie_security_level_safe}</p>";

// Rest of the code continues...
?>
