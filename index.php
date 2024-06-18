<?php
include("./lib/polyfill.php");

$dataFiles = array(
	"zones" => array( "file" => "./data/zones.json"),
	"zoneinfo" => array("file" => "./data/zoneinfo.json")
);

$filterToggleList = array();
$autoAdjustments = array(
	"Chersonese" => array(
		"top" => 30,	"bottom" => -30,
		"left" => -85,	"right" => 85
	),
	"Enmerkar Forest" => array(
		"top" => 0,		"bottom" => 0,
		"left" => 0,	"right" => 0
	),
	"Abrassar" => array(
		"top" => 0,		"bottom" => 0,
		"left" => 0,	"right" => 0
	),
	"Hallowed Marsh" => array(
		"top" => -70,	"bottom" => 70,
		"left" => -60,	"right" => 60
	),
	"Antique Plateau" => array(
		"top" => 0,		"bottom" => 0,
		"left" => 0,	"right" => 0
	),
	"Caldera" => array(
		"top" => 0,		"bottom" => 0,
		"left" => 0,	"right" => 0
	)
);

function generateZoneTabber($data) {
	$out = "";
	
	foreach($data as $k => $v) {
		if($v['img'] == "AUTO")
			$v['img'] = genImgTabber($k);
		
		$out .= '<div tabindex="0" class="tabber--opt" data-target="'.genSafeName($k).'"><div class="tabber--icon"><img src="'.$v['img'].'" /></div><div class="tabber--name">'.$k.'</div></div>';
	}
	
	return $out;
}

function generateMapInfo($data) {
	global $autoAdjustments;
	$out = "";
	
	foreach($data as $k => $v) {
		if($v['img'] == "AUTO")
			$v['img'] = genImgMap($k);
		
		$out .= '<div class="map map__'.$k.'" id="map__'.genSafeName($k).'"><div class="map-base"><img src="'.$v['img'].'"></div>
		<div class="map-overlays">';
		
		// Generate standard location information
		$out .= parseLocs($v['locations'], $autoAdjustments[$k]);
		
		// Load in additional location files
		$additionalMarkers = glob("./data/markers/" . $k . "_*.json");
		foreach($additionalMarkers as $fK => $fD) {
			$fC = json_decode(file_get_contents($fD), true);
			$out .= parseLocs($fC, $autoAdjustments[$k]);
		}
		
		$out .= '</div></div>';
	}
	
	return $out;
}

function parseLocs($locations, $adjustments) {
	$out = '';
	
	foreach($locations as $lK => $lD) {
		$lName = $lD['name'];
		$lReal = isset($lD['realName']) ? $lD['realName'] : "";
		$lTypes = isset($lD['types']) ? $lD['types'] : array("Unmarked");
		$lIcon = genImgIcon((isset($lD['icon']) ? $lD['icon'] : "unmarked.png"));
		
		if(isset($lD['auto_adjust']) && $lD['auto_adjust']) {
			if(isset($lD['left']))
				$lD['left'] += $adjustments['left'];
			if(isset($lD['top']))
				$lD['top'] += $adjustments['top'];
			if(isset($lD['right']))
				$lD['right'] += $adjustments['right'];
			if(isset($lD['bottom']))
				$lD['bottom'] += $adjustments['bottom'];
		}
		
		$lLeft = isset($lD['left']) ? $lD['left'] : null;
		$lTop = isset($lD['top']) ? $lD['top']: null;
		$lRight = isset($lD['right']) ? $lD['right'] : null;
		$lBottom = isset($lD['bottom']) ? $lD['bottom'] : null;
		$lLoc = "";
		
		if($lLeft !== null)
			$lLoc .= "left:".$lLeft.";";
		if($lTop !== null)
			$lLoc .= "top:".$lTop.";";
		if($lRight !== null)
			$lLoc .= "right:".$lRight.";";
		if($lBottom !== null)
			$lLoc .= "bottom:".$lBottom.";";
		
		$out .= generateLocGrp($lLoc, $lTypes, $lIcon, $lName, $lReal);
		
		foreach($lTypes as $tKey => $tVal) {
			addToFilterToggleList($tVal);
		}
	}
	
	return $out;
}

function generateLocGrp($loc, $types, $icon, $name, $real) {
	return '
		<div class="location-group" style="'.$loc.'">
			<div class="icon '.implode(" ", $types).'"><img src="'.$icon.'"></div>
			<div class="location-name">'.$name.'</div>
			<div class="location-real">'.$real.'</div>
			<div class="location-types">'.implode(" ", $types).'</div>
		</div>';
}

function genSafeName($name, $lower = true) {
	if($lower) $name = strtolower($name);
	return str_replace(array(" "), "_", $name);
}

function genImgTabber($name) {
	return "./img/tabber/".genSafeName($name).".webp";
}

function genImgMap($name) {
	return "./img/zones/".genSafeName($name).".png";
}

function genImgIcon($name) {
	return "./img/markers/".$name;
}

function genImgToggle($name) {
	return "./img/markers/".genSafeName($name).".png";
}

function addToFilterToggleList($item) {
	$toIgnore = array("Image-Offset-30");
	
	if(in_array($item, $toIgnore))
		return;
	
	global $filterToggleList;
	$filterToggleList[] = $item;
	$filterToggleList = array_unique($filterToggleList, SORT_REGULAR);
}

function generateFilterToggleList() {
	global $filterToggleList;
	$out = "";
	foreach($filterToggleList as $key => $item) {
		$out .= '<button title="'.$item.'" class="toggle-pin" data-types="'.$item.'"><img src="'.genImgToggle($item).'"></button>';
	}
	
	return $out;
}
?>
<html>
<head>
	<title>Outward - Map Tracker</title>
	<link rel="stylesheet" href="css/main.css">
	<link rel="icon" type="image/x-icon" href="favicon.ico">
	<script src="./js/jquery-3.7.1.min.js"></script>
	<script src="./js/phpjs.js"></script>
	<script src="./js/main.js"></script>
	<script src="./js/grabScroll.js"></script>
	<script src="./js/scrollbarSizing.js"></script>
</head>
<body>
	<div class="section tabber">
		<div class="tabber--zones">
			<div class="tracker--zones">
				<?PHP echo generateZoneTabber(json_decode(file_get_contents($dataFiles['zones']['file']), true)); ?>
			</div>
		</div>
	</div>
	<div class="section maps">
		<div class="maps--container">
			<?PHP echo generateMapInfo(json_decode(file_get_contents($dataFiles['zoneinfo']['file']), true)); ?>
		</div>
		<div class="maps--toolbar">
			<div class="toolbar top left vert">
				<button id="toolbar--zoom-out">+</button>
				<button id="toolbar--zoom-in">-</button>
				<!--<button id="toolbar--text-big">A+</button>
				<button id="toolbar--text-small">A-</button>-->
			</div>
			<div class="toolbar bottom left horz bg-darken">
				<div class="toolbar--status">Zoom: <span id="toolbar--zoom-per">100%</span></div>
				<!--<div class="toolbar--status">Text Size: <span id="toolbar--zoom-text">100%</span></div>-->
			</div>
			<div class="toolbar bottom right hor-r bg-darken horz-wrap">
				<button title="Toggle visibility of marker filters" class="btn-height-48" id="toolbar--toggle-pin">&lt;</button>
				<div id="toggle-pins" style="display: none;">
					<?PHP echo generateFilterToggleList(); ?><div class="btn-grp"><button title="Toggle all filters off" class="btn-red btn-height-48" id="toolbar--filters-off">&#10008;</button><button title="Toggle all filters on" class="btn-green btn-height-48" id="toolbar--filters-on">&#10003;</button></div>
				</div>
			</div>
			<div class="toolbar top right vert bg-darken vert-wrap">
				<div class="toolbar--status">Day: <input id="toolbar--day" value="1" min="0" /></div>
				<button title="Clears completed markers based on given day" id="toolbar--pins-reset-clear"><img src="./img/btn_pins_reset_clear.png"></button>
			</div>
		</div>
	</div>
	<div class="section footer"></div>
</body>
</html>