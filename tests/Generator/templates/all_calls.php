<br /><br /><h1>Implementation Status</h1>

<table class="table table-striped">
	<tr><th>Count</th><th>Verb</th><th>End Point</th><th>Documentation</th><th>Implemented</th><th nowrap="nowrap">Auto Tested</th></tr>
<?php
$last_path = '';
$count = 0;
$done = ['documentation'=> 0,
         'implemented'	=> 0,
         'test'			=> 0];
foreach ($all_paths as $path => $verbs) {
    foreach ($verbs as $verb) {
        $count++; ?>
	<tr>
		<td><?php echo $count ?></td>
		<td><?php echo strtoupper($verb) ?></td>
		<td><?php echo $path; ?></td>
		<td><span class="icon icon-done">Done</span></td>
		<td><?php if (isset($done_paths[$path]) and in_array($verb, $done_paths[$path])) {
            $done['implemented']++;
            echo '<span class="icon icon-done">Done</span>';
        } ?></td>
		<td><?php if (isset($test_calls[$path]) and in_array($verb, $test_calls[$path])) {
            $done['test']++;
            echo '<span class="icon icon-done">Done</span>';
        } ?></td>
	</tr>
<?php
    } ?>
<?php
} ?>
<tr><th>Total</th><th></th><th></th><th><?php echo $count ?></th>
									<th><?php echo $done['implemented'] . "(" . round($done['implemented'] / $count * 100, 2) . "%)"; ?></th>
									<th><?php echo $done['test']. "(" . round($done['test'] / $count * 100, 2) . "%)"; ?></th></tr>
</table>