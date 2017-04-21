<?php

require_once ('config.php');

$content = file_get_contents('php://input');
$json = json_decode($content, true);
$file = fopen(LOGFILE, "a"); // Name for you Log File
$time = time();

date_default_timezone_set('UTC');
fputs($file, date("d-m-Y (H:i:s)",$time) . "\n");

if (!isset($_GET['token']) || $_GET['token'] !== TOKEN) {
	header('HTTP/1.0 403 Forbidden');
	fputs($file, "Access Denied" . "\n");
	exit;
}else{
	if($json['ref'] == BRANCH){
		fputs($file, $content . PHP_EOL);
		if (file_exists(DIR.'.git') && is_dir(DIR)) {
		        try{
	        	        chdir(DIR);
	                	shell_exec(GIT . ' pull');
		        		if (!empty(AFTER_PULL)) {
                            try{
                                shell_exec(AFTER_PULL);
                            }catch (Exception $e) {
                                fputs($file, $e . "\n");
                            }
                        }
	                	fputs($file, "*** AUTO PULL SUCCESFUL ***" . "\n");
	        	}catch (Exception $e) {
	                	fputs($file, $e . "\n");
	        	}
		}
		else {
	        	fputs($file, "DIR Not Found" . "\n");
		}
	}
	else{
		fputs($file, "Push in: " . $json['ref'] . "\n");
	}
}

fputs($file, "\n\n" . PHP_EOL);
fclose($file);
?>
