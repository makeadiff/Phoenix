<h1>Generate Tests</h1>

<form action="" method="post" class="form-area">
<fieldset id="step-1">
	<legend>Step 1</legend>
<?php 
$html->buildInput("swagger_file", "Swagger API File(YAML)", "text", $swagger_file);
$html->buildInput("api_base_path", "Base URL of the API", "text", $api_base_path);
$html->buildInput("path", "Path", "select", $path, array('options' => $all_paths));
$html->buildInput("replaced_url", "URL", "text");
$html->buildInput("verb", "Verb", "select", $verb, array('options' => array('get' => 'get', 'post' => 'post', 'delete' => 'delete')));
$html->buildInput("show-step-2", "&nbsp;", "button", "Show Step 2", array('class' => 'btn btn-primary'));
?>
</fieldset>

<fieldset id="step-2">
	<legend>Step 2</legend>
	<div id="call-error"></div>
	<label for="json">Call Results</label>
	<textarea id="json" rows="15" cols="80" name="json">Loading...</textarea><br />
	<?php
	$html->buildInput("test_type", "Test Type", "select", $test_type, array('options' => $all_test_types));
	?>
	<table>
		<tr><td width="100">&nbsp;</td><td colspan="2"><textarea name="data-assertion" id="data-assertion" rows="5" cols="80"></textarea></td></tr>
		<tr><td>&nbsp;</td><td colspan="2">Insert: <br />
								<input type="button" id="search-array" value="Search Array" class='btn btn-success btn-xs' /> &nbsp;
								<input type="button" id="single-item" value="Single" class='btn btn-success btn-xs' />
							</td></tr>
	</table><br /><br />

	<?php
	$html->buildInput("action", "&nbsp;", "submit", "Generate Tests", array('class' => 'btn btn-primary'));
	?>
</fieldset>
</form>