<br /><br /><h1>Generate Tests</h1>

<form action="" method="post" class="form-area">
<fieldset id="step-1">
	<legend>Step 1</legend>
<?php
$html->buildInput("model", "Model", "select", $model_index, array('options' => $tables));
$html->buildInput("function", "Function", "text", $function);
$html->buildInput("parameters", "Parameters", "text", $parameters);
$html->buildInput("test_name", "Test Name", "text", $test_name);
$html->buildInput("action", "&nbsp;", "submit", "Show Step 2", array('class' => 'btn btn-primary'));
?>
</fieldset>

</form>