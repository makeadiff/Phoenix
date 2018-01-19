function init() {
	// $("#path").on("change", showVariables);
	$("#show-step-2").click(showStepTwo);
	$("#show-step-3").click(showStepThree);
}

function showStepTwo() {
	$("#step-2").show();
	showVariables();
}

function showStepThree() {
	$("#step-3").show();
	getApiData();
}

function getApiData() {
	var api_base_path = $("#api_base_path").val();
	var path = $("#path").val();
	var variables = parsePath(path);
	var replaces = [];
	var url = path;
	for (var i = 0; i < variables.length; i++) {
		url = url.replace(variables[i], $("#var-" + i).val());
	}
	$("#replaced_url").val(url);
	var full_url = api_base_path + url;

	$.ajax({
		url: full_url,
		dataType: "text"
	}).done(function(data) {
		$("#json").val(data);
		// console.log(data);
	})
	
	console.log(url);
}


function showVariables() {
	var ele = $("#path");
	var path = ele.val();
	var variables = parsePath(path);
	var html = "";
	var value;

	for(var i=0; i<variables.length; i++) {
		value = 1
		html += "<label for='var-"+i+"'>"+variables[i]+"</label><input type='text' name='var-"+i+"' id='var-"+i+"' value='"+value+"' /><br />";
	}
	$("#vars").append($(html));
}

// Taken from API.php in exdon/inclues/classes
function parsePath(url) {
	var vars = [];

	return url.match(/\{(\w+)\}/g);
}
