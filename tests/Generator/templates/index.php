<h1>Generate Tests</h1>

<form action="post" class="form-area">
<fieldset class="step-1">
	<legend>Step 1</legend>
<?php 
$html->buildInput("swagger_file", "Swagger API File(YAML)", "text", $swagger_file);
$html->buildInput("api_base_path", "Base URL of the API", "text", $api_base_path);
$html->buildInput("path", "Path", "text", $path);
$html->buildInput("verb", "Verb", "select", $verb, array('options' => array('get' => 'get', 'post' => 'post', 'delete' => 'delete')));
// $html->buildInput("action", "&nbsp;", "submit", "Show Step 2", array('class' => 'btn btn-primary'));
?>
</fieldset>



</form>