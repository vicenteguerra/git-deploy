<?php
$content = file_get_contents("php://input");
$json    = json_decode($content, true);
$file    = fopen(LOGFILE, "a");
$time    = time();
$token   = false;
$sha     = false;
$DIR     = preg_match("/\/$/", DIR) ? DIR : DIR . "/";

// retrieve the token
if (!$token && isset($_SERVER["HTTP_X_HUB_SIGNATURE"])) {
    list($algo, $token) = explode("=", $_SERVER["HTTP_X_HUB_SIGNATURE"], 2) + array("", "");
} elseif (isset($_SERVER["HTTP_X_GITLAB_TOKEN"])) {
    $token = $_SERVER["HTTP_X_GITLAB_TOKEN"];
} elseif (isset($_GET["token"])) {
    $token = $_GET["token"];
}

// retrieve the checkout_sha
if (isset($json["checkout_sha"])) {
    $sha = $json["checkout_sha"];
} elseif (isset($_SERVER["checkout_sha"])) {
    $sha = $_SERVER["checkout_sha"];
} elseif (isset($_GET["sha"])) {
    $sha = $_GET["sha"];
}

// write the time to the log
date_default_timezone_set("UTC");
fputs($file, date("d-m-Y (H:i:s)", $time) . "\n");

// specify that the response does not contain HTML
header("Content-Type: text/plain");

// use user-defined max_execution_time
if (!empty(MAX_EXECUTION_TIME)) {
    ini_set("max_execution_time", MAX_EXECUTION_TIME);
}

// function to forbid access
function forbid($file, $reason) {
    // format the error
    $error = "=== ERROR: " . $reason . " ===\n*** ACCESS DENIED ***\n";

    // forbid
    http_response_code(403);

    // write the error to the log and the body
    fputs($file, $error . "\n\n");
    echo $error;

    // close the log
    fclose($file);

    // stop executing
    exit;
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
        if (file_exists($DIR . ".git") && is_dir($DIR)) {
            // change directory to the repository
            chdir($DIR);

            // write to the log
            fputs($file, "*** AUTO PULL INITIATED ***" . "\n");

            /**
             * Attempt to reset specific hash if specified
             */
            if (!empty($_GET["reset"]) && $_GET["reset"] === "true") {
                // write to the log
                fputs($file, "*** RESET TO HEAD INITIATED ***" . "\n");

                exec(GIT . " reset --hard HEAD 2>&1", $output, $exit);

                // reformat the output as a string
                $output = (!empty($output) ? implode("\n", $output) : "[no output]") . "\n";

                // if an error occurred, return 500 and log the error
                if ($exit !== 0) {
                    http_response_code(500);
                    $output = "=== ERROR: Reset to head failed using GIT `" . GIT . "` ===\n" . $output;
                }

                // write the output to the log and the body
                fputs($file, $output);
                echo $output;
            }

            /**
             * Attempt to execute BEFORE_PULL if specified
             */
            if (!empty(BEFORE_PULL)) {
                // write to the log
                fputs($file, "*** BEFORE_PULL INITIATED ***" . "\n");

                // execute the command, returning the output and exit code
                exec(BEFORE_PULL . " 2>&1", $output, $exit);

                // reformat the output as a string
                $output = (!empty($output) ? implode("\n", $output) : "[no output]") . "\n";

                // if an error occurred, return 500 and log the error
                if ($exit !== 0) {
                    http_response_code(500);
                    $output = "=== ERROR: BEFORE_PULL `" . BEFORE_PULL . "` failed ===\n" . $output;
                }

                // write the output to the log and the body
                fputs($file, $output);
                echo $output;
            }

            /**
             * Attempt to pull, returing the output and exit code
             */
            exec(GIT . " pull 2>&1", $output, $exit);

            // reformat the output as a string
            $output = (!empty($output) ? implode("\n", $output) : "[no output]") . "\n";

            // if an error occurred, return 500 and log the error
            if ($exit !== 0) {
                http_response_code(500);
                $output = "=== ERROR: Pull failed using GIT `" . GIT . "` and DIR `" . DIR . "` ===\n" . $output;
            }

            // write the output to the log and the body
            fputs($file, $output);
            echo $output;

            /**
             * Attempt to checkout specific hash if specified
             */
            if (!empty($sha)) {
                // write to the log
                fputs($file, "*** RESET TO HASH INITIATED ***" . "\n");

                exec(GIT . " reset --hard {$sha} 2>&1", $output, $exit);

                // reformat the output as a string
                $output = (!empty($output) ? implode("\n", $output) : "[no output]") . "\n";

                // if an error occurred, return 500 and log the error
                if ($exit !== 0) {
                    http_response_code(500);
                    $output = "=== ERROR: Reset failed using GIT `" . GIT . "` and \$sha `" . $sha . "` ===\n" . $output;
                }

                // write the output to the log and the body
                fputs($file, $output);
                echo $output;
            }

            /**
             * Attempt to execute AFTER_PULL if specified
             */
            if (!empty(AFTER_PULL)) {
                // write to the log
                fputs($file, "*** AFTER_PULL INITIATED ***" . "\n");

                // execute the command, returning the output and exit code
                exec(AFTER_PULL . " 2>&1", $output, $exit);

                // reformat the output as a string
                $output = (!empty($output) ? implode("\n", $output) : "[no output]") . "\n";

                // if an error occurred, return 500 and log the error
                if ($exit !== 0) {
                    http_response_code(500);
                    $output = "=== ERROR: AFTER_PULL `" . AFTER_PULL . "` failed ===\n" . $output;
                }

                // write the output to the log and the body
                fputs($file, $output);
                echo $output;
            }

            // write to the log
            fputs($file, "*** AUTO PULL COMPLETE ***" . "\n");
        } else {
            // prepare the generic error
            $error = "=== ERROR: DIR `" . DIR . "` is not a repository ===\n";

            // try to detemrine the real error
            if (!file_exists(DIR)) {
                $error = "=== ERROR: DIR `" . DIR . "` does not exist ===\n";
            } elseif (!is_dir(DIR)) {
                $error = "=== ERROR: DIR `" . DIR . "` is not a directory ===\n";
            }

            // bad request
            http_response_code(400);

            // write the error to the log and the body
            fputs($file, $error);
            echo $error;
        }
    } else{
        $error = "=== ERROR: Pushed branch `" . $json["ref"] . "` does not match BRANCH `" . BRANCH . "` ===\n";

        // bad request
        http_response_code(400);

        // write the error to the log and the body
        fputs($file, $error);
        echo $error;
    }
}

// close the log
fputs($file, "\n\n" . PHP_EOL);
fclose($file);
