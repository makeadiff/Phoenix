<h1>Generate Tests</h1>

<form action="" method="post" class="form-area">
<fieldset id="step-1">
	<legend>Step 1</legend>
<?php 
$html->buildInput("swagger_file", "Swagger API File(YAML)", "text", $swagger_file);
$html->buildInput("api_base_path", "Base URL of the API", "text", $api_base_path);
$html->buildInput("path", "Path", "select", $path, array('options' => $all_paths));
$html->buildInput("test_type", "Test Type", "select", $test_type, array('options' => array('single' => 'Single', 'search' => 'Search', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete')));
$html->buildInput("verb", "Verb", "select", $verb, array('options' => array('get' => 'get', 'post' => 'post', 'delete' => 'delete')));
$html->buildInput("show-step-2", "&nbsp;", "button", "Show Step 2", array('class' => 'btn btn-primary'));
?>
</fieldset>

<fieldset id="step-2">
	<legend>Step 2</legend>

	<div id="vars"></div>
	<?php
	$html->buildInput("show-step-3", "&nbsp;", "button", "Show Step 3", array('class' => 'btn btn-primary'));
	?>
</fieldset>


<fieldset id="step-3">
	<legend>Step 3</legend>
	<label for="json">Call Results</label>
	<textarea id="json" rows="15" cols="80" name="json">Loading...</textarea><br />

	<table>
		<tr><td width="200">Path</td><td>Value</td></tr>
		<tr><td><input name="data-path[]" value="" /></td><td><input name="data-value[]" value="" /></td></tr>
		<tr><td><input name="data-path[]" value="" /></td><td><input name="data-value[]" value="" /></td></tr>
		<tr><td><input name="data-path[]" value="" /></td><td><input name="data-value[]" value="" /></td></tr>
	</table>
	<?php
	$html->buildInput("replaced_url", "URL", "text", "");
	$html->buildInput("action", "&nbsp;", "submit", "Generate Tests", array('class' => 'btn btn-primary'));
	?>
</fieldset>
</form>