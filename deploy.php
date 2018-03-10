<?php
require_once("config.php");

$content = file_get_contents("php://input");
$json    = json_decode($content, true);
$file    = fopen(LOGFILE, "a");
$time    = time();
$token   = false;

// retrieve the token
if (!$token && isset($_SERVER["HTTP_X_HUB_SIGNATURE"])) {
    list($algo, $token) = explode("=", $_SERVER["HTTP_X_HUB_SIGNATURE"], 2) + array("", "");
} elseif (isset($_SERVER["HTTP_X_GITLAB_TOKEN"])) {
    $token = $_SERVER["HTTP_X_GITLAB_TOKEN"];
} elseif (isset($_GET["token"])) {
    $token = $_GET["token"];
}

// log the time
date_default_timezone_set("UTC");
fputs($file, date("d-m-Y (H:i:s)", $time) . "\n");

// function to forbid access
function forbid($file, $reason) {
    // explain why
    if ($reason) fputs($file, "=== ERROR: " . $reason . " ===\n");
    fputs($file, "*** ACCESS DENIED ***" . "\n\n\n");
    fclose($file);

    // forbid
    header("HTTP/1.0 403 Forbidden");
    exit;
}

// function to return OK
function ok() {
    ob_start();
    header("HTTP/1.1 200 OK");
    header("Connection: close");
    header("Content-Length: " . ob_get_length());
    ob_end_flush();
    ob_flush();
    flush();
}

// Check for a GitHub signature
if (!empty(TOKEN) && isset($_SERVER["HTTP_X_HUB_SIGNATURE"]) && $token !== hash_hmac($algo, $content, TOKEN)) {
    forbid($file, "X-Hub-Signature does not match TOKEN");
// Check for a GitLab token
} elseif (!empty(TOKEN) && isset($_SERVER["HTTP_X_GITLAB_TOKEN"]) && $token !== TOKEN) {
    forbid($file, "X-GitLab-Token does not match TOKEN");
// Check for a $_GET token
} elseif (!empty(TOKEN) && isset($_GET["token"]) && $token !== TOKEN) {
    forbid($file, "\$_GET[\"token\"] does not match TOKEN");
// if none of the above match, but a token exists, exit
} elseif (!empty(TOKEN) && !isset($_SERVER["HTTP_X_HUB_SIGNATURE"]) && !isset($_SERVER["HTTP_X_GITLAB_TOKEN"]) && !isset($_GET["token"])) {
    forbid($file, "No token detected");
} else {
    // check if pushed branch matches branch specified in config
    if ($json["ref"] === BRANCH) {
        fputs($file, $content . PHP_EOL);

        // ensure directory is a repository
        if (file_exists(DIR . ".git") && is_dir(DIR)) {
            try {
                // pull
                fputs($file, "*** AUTO PULL INITIATED ***" . "\n");
                chdir(DIR);
                $result = shell_exec(GIT . " pull 2>&1");

                fputs($file, $result . "\n");

                // return OK to prevent timeouts on AFTER_PULL
                ok();

                // execute AFTER_PULL if specified
                if (!empty(AFTER_PULL)) {
                    try {
                        fputs($file, "*** AFTER_PULL INITIATED ***" . "\n");
                        $result = shell_exec(AFTER_PULL);
                        fputs($file, $result . "\n");
                    } catch (Exception $e) {
                        fputs($file, $e . "\n");
                    }
                }
                fputs($file, "*** AUTO PULL COMPLETE ***" . "\n");
            } catch (Exception $e) {
                fputs($file, $e . "\n");
            }
        } else {
            fputs($file, "=== ERROR: DIR is not a repository ===" . "\n");
        }
    } else{
        fputs($file, "=== ERROR: Pushed branch does not match BRANCH ===\n");
    }
}

fputs($file, "\n\n" . PHP_EOL);
fclose($file);
