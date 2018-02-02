var data_object_name = false;

function init() {
	$("#path").on("change", setUrl);
	$("#show-step-2").click(showStepTwo);
	$("#search-array").click(function() { codeInsert('search-array'); });
}

function codeInsert(type) {
	var code;

	if(type == 'search-array') {
		code = `        $search_for = 'INSERT VALUE FOR SEARCH';
        $found = false;
        foreach ($data->data->INSERT_OBJECT_NAME as $key => $info) {
            if($info->INSERT_KEY == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);`;
	}

	if(data_object_name) code = code.replace('INSERT_OBJECT_NAME', data_object_name);

	$('#data-assertion').val(code);
}
function setUrl() {
	var path = $("#path").val();
	var variables = parsePath(path);
	var replaces = {
		"{center_id}": 	220, 	// Start Rek
		"{user_id}": 	1,		// Binny
		"{batch_id}": 	1971,	// Batch in Start Rek
		"{level_id": 	4852,	// Level in Start Rek
		"{city_id}": 	28,		// Test City
		"{group_id}": 	9		// ES Volunteer
	}

	var url = path;
	if(variables) {
		for(var i=0; i<variables.length; i++) 
			url = url.replace(variables[i], replaces[variables[i]]);
	}

	$("#replaced_url").val(url);
}

function showStepTwo() {
	$("#step-2").show();
	getApiData();
}

function getApiData() {
	var api_base_path = $("#api_base_path").val();
	// var path = $("#path").val();
	// var variables = parsePath(path);
	// var replaces = [];
	// var url = path;
	// for (var i = 0; i < variables.length; i++) {
	// 	url = url.replace(variables[i], $("#var-" + i).val());
	// }
	// $("#replaced_url").val(url);
	var url = $("#replaced_url").val();
	var full_url = api_base_path + url;

	$.ajax({
		url: full_url,

	}).done(function(data) {
		var all_keys = Object.keys(data['data']);
		data_object_name = all_keys[0];

		if(Array.isArray(data['data'][data_object_name])) {
			$("#test_type option[value='list']").prop('selected', true);
		}

		$("#json").val(JSON.stringify(data, null, 4));
	}).error(function(data) {
		var response = JSON.parse(data.responseText);
		$("#json").val(JSON.stringify(response, null, 4));
	});
}

// Taken from API.php in exdon/inclues/classes
function parsePath(url) {
	var vars = [];

	return url.match(/\{(\w+)\}/g);
}
