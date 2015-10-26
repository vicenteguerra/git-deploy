<?
require_once ('config.php');

$content = file_get_contents('php://input');
$json = json_decode($content, true);
$file = fopen("log.txt", "a"); // Name for you Log File
$time = time();

fputs($file, date("d-m-Y (H:i:s)" . EOL,$time));

if (!isset($_GET['token']) || $_GET['token'] !== TOKEN) {
	header('HTTP/1.0 403 Forbidden');
	fputs($file, "Access Denied" . EOL);
	exit;
}else{
	if($json['ref'] == "branch-name"){ // Example of "branch name": refs/heads/Dev or refs/heads/master
		fputs($file, $content . EOL);
		if (!file_exists(DIR.'.git') || !is_dir(DIR)) {
		        try{
	        	        chdir(DIR);
	                	shell_exec('/usr/bin/git pull');
	                	fputs($file, "*** AUTO PULL SUCCESFUL ***" . PHP_EOL);
	        	}catch (Exception $e) {
	                	fwrite($file, $e . PHP_EOL);
	        	}
		}
		else {
	        	fputs($file, "DIR Not Found" . PHP_EOL);
		}
	}
	else{
		fputs($file, "Push in: " . $json['ref'] . EOL);
	}
}

fclose($file);
?>