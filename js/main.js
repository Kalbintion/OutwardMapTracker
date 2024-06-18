function changeTabberOpt() {
	$(".map.active").removeClass("active");
	$(".tabber--opt.active").removeClass("active");
	
	$("#map__" + $(this).data('target')).addClass("active");
	$(this).addClass("active");
}

function updateBuildURL() {
	var out = "";
	
	document.location.hash = "v1_" + out.slice(0, -1);
}

function loadBuildURL() {
	let skillHash = location.hash.replaceAll("#", "");
	if(skillHash == "")
		return;
	
	let skillCount = $("input").length;
	let buildVersion = skillHash.split("_")[0];
	let buildVersionNumber = buildVersion.replaceAll("v", "");
	let skillData = skillHash.split("_").slice(1);
	console.log("Loading", skillHash, skillHash.length, skillCount);
	
	var buildLoaders = {1: loadBuildV1};
	buildLoaders[buildVersionNumber](skillHash, buildVersion, skillData);
}

function loadBuildV1(skillHash, buildVersion, skillData) {
	let skillCount = $("input").length;
	
	if(buildVersion !== "v1") {
		console.error("Could not load build! Build version not supported.");
	}
	
	if(skillData.length > skillCount) {
		console.error("Could not load build! Data mismatch.");
		return;
	}
	
	// We're good to load
	let numInput = $("input[type=number]").length;
	$("input[type=number]").each((k, v) => {
		$(v).val(skillData[k]);
	});
	$("input[type=checkbox]").each((k, v) => {
		v.checked = (skillData[k + numInput] == 1 ? true : false);
	});
}

function genSafeName(name, lower = true) {
	if(lower) name = name.toLowerCase();
	name = name.str_replace([" ", "'"], ["_", "\\'"]);
	
	return name;
}

function zoomIn() {
	window.zoomValue -= 0.1;
	window.zoomValue = Math.max(window.zoomValue, 0.5);
	zoomUpdate();
}

function zoomOut() {
	window.zoomValue += 0.1;
	window.zoomValue = Math.min(window.zoomValue, 2);
	zoomUpdate();
}

function zoomUpdate() {
	$(".maps--container").css("zoom", window.zoomValue);
	$("#toolbar--zoom-per").text(Math.round(window.zoomValue * 100) + "%");
	
}

function zoomTextIn() {
	window.textValue -= 0.1;
	window.textValue = Math.max(window.textValue, 0.5);
	zoomTextUpdate();
}

function zoomTextOut() {
	window.textValue += 0.1;
	window.textValue = Math.min(window.textValue, 2);
	zoomTextUpdate();
}

function zoomTextUpdate() {
	$(".maps--container").css("font-size", window.textValue + "em");
	$("#toolbar--zoom-text").text(Math.round(window.textValue * 100) + "%");
}

function updateMapPinVisibility() {
	var allFilters = $("button.toggle-pin.inactive");
	var allPins = $("div.location-group");
	var filters = [];
	
	// compile inactive filter array
	allFilters.each((k,v) => {
		filters = filters.concat($(v).data('types').split(" "));
	});
	
	// inactive filter uniqueness
	filters = [...new Set(filters)];
	
	// parse all pins
	allPins.each((k,v) => {
		$(v).show();
		
		let pinTypes = $(v).children(".location-types").text();
		if(filters.some((substr) => pinTypes.includes(substr)))
			$(v).hide();
	});
}

$(function() {
	// Handlers
	$(".tabber--opt").click(changeTabberOpt);
	$("#toolbar--zoom-in").click(zoomIn);
	$("#toolbar--zoom-out").click(zoomOut);
	$("#toolbar--text-small").click(zoomTextIn);
	$("#toolbar--text-big").click(zoomTextOut);
	$("#toolbar--toggle-pin").click(() => {
		var ret = $("#toggle-pins").toggle();
		if(ret.css("display") == "none")
			$("#toolbar--toggle-pin").text("<");
		else
			$("#toolbar--toggle-pin").text(">");
	});
	$(".toggle-pin").click(function () {
		$(this).toggleClass("inactive");
		updateMapPinVisibility();
	});
	$("#toolbar--filters-off").click(() => {
		$(".toggle-pin").addClass("inactive");
		updateMapPinVisibility();
	});
	$("#toolbar--filters-on").click(() => {
		$(".toggle-pin.inactive").removeClass("inactive");
		updateMapPinVisibility();
	});
	
	// Default settings
	window.zoomValue = 1;
	window.textValue = 1;
	
	// Map Grab scroll
	window.grabScroller = new grabScroll($(".section.maps")[0]);
	
	// Default map open
	$($(".tabber--opt")[0]).click();

	// Build load handler
	window.onhashchange = loadBuildURL();
});