<?php
require('iframe.php');
/// Purpose : Reads Swagger YAML Files and generate PhpUnit Test files for those calls.

$html = new HTML;

$swagger_file = '/mnt/x/Data/www/Projects/Phoenix/api/swagger/swagger.yaml';
$api = yaml_parse(file_get_contents($swagger_file));
$api_base_path = 'http://localhost/Projects/Phoenix/public';

$path = i($QUERY,'path', '/users/{user_id}');
$verb = i($QUERY, 'verb', 'get');
$action = i($QUERY, 'action');

$templates = array();
$templates['single'] =<<<END
	public function testGet%TABLE%Single()
    {
        if(\$this->only_priority_tests) \$this->markTestSkipped("Running only priority tests.");

        \$this->get('%URL%');
        \$data = json_decode(\$this->response->getContent());

        \$this->assertEquals(\$data->status, 'success');
        \$this->assertEquals(%DATA-PATH%, '%DATA-VALUE%'');
        \$this->assertEquals(200, \$this->response->status());
    }
END;

$templates['single-not-found'] =<<<END
    public function testGet%TABLE%SingleNotFound()
    {
        if(\$this->only_priority_tests) \$this->markTestSkipped("Running only priority tests.");

        \$this->get('%URL%');
        \$data = json_decode(\$this->response->getContent());

        \$this->assertEquals(\$data->status, 'error');
        \$this->assertEquals(\$data->message, "%DATA-MESSAGE%");
        \$this->assertEquals(404, \$this->response->status());
    }
END;

$tables = ['User', 'Group', 'City', 'Class', 'Batch', 'Level', 'Center', 'Student'];

if($url) $variables = parsePath($url);

if($action == 'Show Step 2') {
	$input = array();

	foreach ($variables as $var) {
		$input["{" . $var . "}"] = 1; //readline("Enter $var: ");

		// if(isset($data['get']['responses']['404']))
		// 	$input["{" . $var . "}-404"] = readline("Enter $var that will return 404: ");
	}

	// dump($input);

	// Getting data - to confirm that its correct.
	$replaced_url = str_replace(array_keys($input), array_values($input), $url);

} elseif($action == 'Show Step 3') {

	foreach ($api['paths'] as $url => $data) {
		if($url != $path) continue;

		echo $url . "\n";
		$root = preg_replace('/^\/([^\/]+)\/?.*/', '$1', $url);

		// Figure out which table this belongs to.
		$max_match = 0;
		$table = false;
		foreach ($tables as $t) {
			$similarity = similar_text($t, $root);
			if($max_match < $similarity) {
				$max_match = $similarity;
				$table = $t;
			}
		}

		// echo "\t" . $table . "\n";
		if(isset($data['get']['tags'])) {
			// echo "\t" . implode(",", $data['get']['tags']) . "\n";
			if(!in_array('single', $data['get']['tags'])) continue; // Right now, checking for only single calls.
		}

		// $response = load($api_base_path . $replaced_url);
		// print "URL: " . $api_base_path . $replaced_url . "\n";
		// print "Response: " . $response . "\n";
		// $data = json_decode($response);


		$replaces = array(
			'%URL%'			=> $replaced_url,
			'%TABLE%'		=> $table,
			'%DATA-PATH%'	=> '',
			'%DATA-MESSAGE%'=> '',
			'%DATA-VALUE%'	=> '',
		);
	}
}

render();

// Taken from API.php in exdon/inclues/classes
function parsePath($url) {
	$vars = array();

	if(preg_match_all('#\{(\w+)\}#', $url, $matches)) {
		// First we convert the action route with its {variable_name} format to a preg-able string...
		for($i=0; $i<count($matches[0]); $i++) {
			$str = $matches[0][$i];
			$vars[] = $matches[1][$i]; // Get the list of variables in the route into a different array.
		}

		// // Match - assign the values to the assoc array for return.
		// $url_variables = array();
		// for($i=0; $i<count($vars); $i++) {
		// 	$url_variables[$vars[$i]] = $route_matches[$i+1][0];
		// }
	}

	return $vars;
}
