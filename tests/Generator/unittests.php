<?php
require('iframe.php');
$html = new HTML;

$app = require('includes/Phonenix.php');

/// Purpose : 

$tables = ['User', 'Group', 'City', 'Class', 'Batch', 'Level', 'Center', 'Student'];
$model_index = i($QUERY, 'model');
$model_name = '';
if($model_index !== false) $model_name = $tables[$model_index];
$function = i($QUERY, 'function');
$parameters = i($PARAM, 'parameters');
$test_name = i($QUERY, 'test_name');
$action = i($QUERY, 'action');

$templates = array();
$templates['data-assertion'] = "\$this->assertSame(%DATA-KEY%, '%DATA-VALUE%');\n";
$templates['search'] = file_get_contents('code/unit-search.txt');

$all_test_types = [	'single'=> 'Single',
                    '404'	=> 'Not Found - 404',
                    'list'	=> 'List',
                    'search'=> 'Search',
                    'create'=> 'Create',
                    'edit'	=> 'Edit',
                    'delete'=> 'Delete'];
$test_type = 'search';


if($action == 'Show Step 2') {
    eval("use App\Models\{$model_name};
        \$model = new $model_name;
        \$result = \$model->{$function}({$parameters});");//exit;

    if($result) {
        ob_start();
        var_dump($result, 1);
        $dump_code = ob_get_clean();

        $id = $result[0]->id;

        $data_key = '$result->id';
        $data_value = $id;

        $replaces = array(
            '%TABLE%'       => $model_name,
            '%NAME%'        => $test_name,
            '%PARAMETERS%'  => $parameters,
            '%FUNCTION%'    => $function,
            '%DATA-KEY%'    => $data_key,
            '%DATA-VALUE%'  => $data_value,
        );

        $code = str_replace(
            array_keys($replaces),
            array_values($replaces),
            $templates[$test_type]
        );

        render('output.php');
        exit;

    }
    exit;
}

render();