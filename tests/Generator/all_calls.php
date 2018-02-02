<?php
require('iframe.php');

$swagger_file = '/mnt/x/Data/www/Projects/Phoenix/api/swagger/swagger.yaml';
$api = yaml_parse(file_get_contents($swagger_file));
$api_base_path = 'http://localhost/Projects/Phoenix/public';

$all_paths = array();
foreach ($api['paths'] as $path => $data) {
	$all_paths[$path] = array_keys($data);
}

$done_paths = array();
$routes = '/mnt/x/Data/www/Projects/Phoenix/app/Http/routes.php';
$route_lines = explode("\n", file_get_contents($routes));
foreach ($route_lines as $l) {
	if(preg_match('/^\$app\-\>([^\(]+)\(\'([^\']+)\'\,/', $l, $matches)) {
		$verb = $matches[1];
		$path = $matches[2];

		if(!isset($done_paths[$path])) $done_paths[$path] = [$verb];
		else $done_paths[$path][] = $verb;
	}
}

$tested_paths = [];
$test_files_location = '/mnt/x/Data/www/Projects/Phoenix/tests/';
$files = ls('*.php', $test_files_location);
$test_calls = [];
foreach ($files as $f) {
	if($f == 'TestCase.php') continue;

	$contents = file_get_contents(joinPath($test_files_location, $f));
	$lines = explode("\n", $contents);
	foreach ($lines as $l) {
		// if(preg_match('/\$this\-\>get\(\'(.+)\'\)\;/', $l, $matches)) {
		if(preg_match('#\/+ Path\:\s*(\S+)\s*(\S+)#', $l, $matches)) {
			$url = $matches[2];
			$verb = strtolower($matches[1]);
			if(!isset($test_calls[$url]))	$test_calls[$url] = [$verb];
			else $test_calls[$url][] = $verb;
		}
	}
}



// $difference = $all_paths;

// foreach ($difference as $path => $verbs) {
// 	if(isset($done_paths[$path])) {
// 		// dump($verbs);
// 		$diff = array_diff($verbs, $done_paths[$path]);
// 		if(count($diff)) {
// 			$difference[$path] = $diff;
// 		} else {
// 			unset($difference[$path]);
// 		}
// 	}
// }

// dump($difference);

$config['site_title'] = 'Implementation Status';
showTop("API Calls Implementation List");
?>
<table class="table table-striped">
	<tr><th>Count</th><th>End Point</th><th>Verb</th><th>Documentation</th><th>Implemented</th><th>Auto Tested</th></tr>
<?php 
$last_path = '';
$count = 0;
$done = ['documentation'=> 0,
		 'implemented'	=> 0,
		 'test'			=> 0];
foreach ($all_paths as $path => $verbs) { 
	foreach($verbs as $verb) { 
		$count++; ?>
	<tr>
		<td><?php echo $count ?></td>
		<td><?php echo $path; ?></td>
		<td><?php echo $verb ?></td>
		<td><span class="icon done">Done</span></td>
		<td><?php if(isset($done_paths[$path]) and in_array($verb, $done_paths[$path])) {
			$done['implemented']++;
			echo '<span class="icon done">Done</span>';
		} ?></td>
		<td><?php if(isset($test_calls[$path]) and in_array($verb, $test_calls[$path])) {
			$done['test']++;
			echo '<span class="icon done">Done</span>';
		} ?></td>
	</tr>
<?php } ?>
<?php } ?>
<tr><th>Total</th><th></th><th></th><th><?php echo $count ?></th>
									<th><?php echo $done['implemented'] . "(" . round($done['implemented'] / $count * 100, 2) . "%)"; ?></th>
									<th><?php echo $done['test']. "(" . round($done['test'] / $count * 100, 2) . "%)"; ?></th></tr>
</table>