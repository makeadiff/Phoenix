var data_object_name = false;
var data_object_default_check_attribute_name = false;
var data_object_default_check_attribute_value = false;

function init() {
	$("#path").on("change", setUrl);
	$("#show-step-2").click(showStepTwo);
	$("#search-array").click(function() { codeInsert('search-array'); });
	$("#single-item").click(function() { codeInsert('single-item'); });
}

function codeInsert(type) {
	var code;

	if(type == 'search-array') {
		code = `$search_for = 'INSERT_VALUE_FOR_SEARCH';
        $found = false;
        foreach ($data->data->INSERT_OBJECT_NAME as $key => $info) {
            if($info->INSERT_KEY == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);`;
	} else if(type == 'single-item') {
		code = `$this->assertEquals($data->data->INSERT_OBJECT_NAME->INSERT_KEY, 'INSERT_VALUE_FOR_SEARCH');`;
	}

	if(data_object_name) code = code.replace('INSERT_OBJECT_NAME', data_object_name);
	if(data_object_default_check_attribute_name) code = code.replace('INSERT_KEY', data_object_default_check_attribute_name);
	if(data_object_default_check_attribute_value) code = code.replace('INSERT_VALUE_FOR_SEARCH', data_object_default_check_attribute_value);

	$('#data-assertion').val(code);
}
function setUrl() {
	var path = $("#path").val();
	var variables = parsePath(path);
	var replaces = {
		"{center_id}": 	220, 	// Start Rek
		"{user_id}": 	1,		// Binny
		"{batch_id}": 	1971,	// Batch in Start Rek
		"{level_id}": 	4852,	// Level in Start Rek
		"{city_id}": 	28,		// Test City
		"{group_id}": 	9,		// ES Volunteer
		"{student_id}": 21932,	// Yoda
		"{event_id}": 	2069,
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
	var url = $("#replaced_url").val();
	var full_url = api_base_path + url;

	$.ajax({
		url: full_url,
		beforeSend: function (xhr) {
		    xhr.setRequestHeader ("Authorization", "Basic " + btoa("sulu.simulation@makeadiff.in:pass"));
		},
	}).done(function(data) {
		var all_keys = Object.keys(data['data']);
		data_object_name = all_keys[0];

		if(Array.isArray(data['data'][data_object_name])) {
			$("#test_type option[value='list']").prop('selected', true);

			var random_index =  Math.floor((Math.random() * data['data'][data_object_name].length)); 
			setDataAttribute(data['data'][data_object_name][random_index]);
		} else {
			setDataAttribute(data['data'][data_object_name]);
		}
		console.log(data_object_default_check_attribute_name, data_object_default_check_attribute_value);

		$("#json").val(JSON.stringify(data, null, 4));
	}).error(function(data) {
		try {
			var response = JSON.parse(data.responseText);
			$("#json").val(JSON.stringify(response, null, 4));
		} catch (e) {
			$("#call-error").html("<h3 class='error'>Error...</h3>Call Error: " + e + "<br /><a href='" + full_url + "'>Go to " + url + "</a>");
		}
		
	});
}

// Taken from API.php in exdon/inclues/classes
function parsePath(url) {
	var vars = [];

	return url.match(/\{(\w+)\}/g);
}

function setDataAttribute(obj) {
	var keys = Object.keys(obj);
	var keys_to_check_for = ['name', 'id'];

	for(var i in keys_to_check_for) {
		var k = keys_to_check_for[i];

		if(typeof obj[k] !== "undefined") {
			data_object_default_check_attribute_name = k;
			data_object_default_check_attribute_value = obj[k];
			break;
		}
	}
}
