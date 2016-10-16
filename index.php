<?php

require 'vendor/autoload.php';
use Flintstone\Flintstone;

$mode = trim($_REQUEST['mode']);

function pre($ar) {
	echo '<pre>';
	print_r($ar);
	echo '</pre>';
}

function array_to_xml( $data, &$xml_data ) {
    foreach( $data as $key => $value ) {
    	if( is_numeric($key) ){
                $key = 'item'.$key; //dealing with <0/>..<n/> issues
            }
        if( is_array($value) ) {
            
            $subnode = $xml_data->addChild($key);
            array_to_xml($value, $subnode);
        } else {
            $xml_data->addChild("$key",htmlspecialchars("$value"));
        }
     }
}

function getFlinstone($db) {
	$options = array('dir' => './');
	return new Flintstone($db, $options);;

}

function returnData($data, $output=1) {
	if($output==0) {
		pre($data);
	} elseif($output==1) {
		// creating object of SimpleXMLElement
		$xml_data = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><data></data>');

		// function call to convert array to xml
		array_to_xml($data,$xml_data);

		//saving generated xml file; 
		// $result = $xml_data->asXML('/file/path/name.xml');
		echo $xml_data->asXML();
	} elseif($output==2) {
		if(count($output)>0) echo $data[0];
			else echo "";
	}
}

function getStrKey($arKeys) {
	return implode("---", $arKeys);
}

function doGet($project, $regname, $arKeys) {
	$db = getFlinstone($project);

	$arAllData = $db->get("data");
	if(empty($arAllData)) returnData("");
	if(!isset($arAllData[$regname])) returnData("");

	 // pre($arAllData[$regname]);
	 // pre($arKeys);
	foreach ($arAllData[$regname] as $arDataKey) {
		$isFound = true;
		// pre($arDataKey['keys']);
		for($i=0;$i<count($arKeys);$i++) {
			// spre($arDataKey['keys'][$i].' - '.$arKeys[$i]);
			if($arDataKey['keys'][$i]!=$arKeys[$i]) {
				$isFound=false;
				break;
			}
		}	
		if($isFound) {
			returnData($arDataKey['values']);
		}
	}


	returnData("");
}

function doSet($project, $regname, $arVals) {
	// TODO: проверить $arVals на соответствие по метаданным
	$db = getFlinstone($project);
	$arAllData = $db->get("data");
	// pre($arVals);
	if(empty($arAllData)) $arAllData = array();
	if(!isset($arAllData[$regname])) $arAllData[$regname] = array();

	$arData = $arAllData[$regname];
	
	if(empty($arData)) $arData = array();

	for($curKeyIndex=0;$curKeyIndex<count($arData);$curKeyIndex++) {
		$arDataKey = $arData[$curKeyIndex];
		// pre($curKeyIndex);
		// pre($arDataKey);
		if(count($arDataKey['keys'])==0) break;
		$isFound = true;

		for($i=0;$i<count($arVals['keys']);$i++) {
			if($arDataKey['keys'][$i]!=$arVals['keys'][$i]) {
				$isFound=false;
				break;
			}
		}
		// pre("found - $isFound");
		if($isFound) {
			$arAllData[$regname][$curKeyIndex]['values'] = $arVals['values'];
			$db->set("data", $arAllData);
			// pre($arAllData);
			returnData(array('ok'));
			return;
		}
	}
	$arAllData[$regname][] = $arVals;

	// pre($arAllData);
	$db->set("data", $arAllData);

	returnData(array('ok'));
}

function doGetKeys($project) {
	$db = getFlinstone($project);
	returnData($db->getKeys());
}

function doSetMeta($project, $regname, $metakeys) {
	$db = getFlinstone($project);
	// TODO - нужно проверять то, что передали в $metakeys
	$arMeta = $db->get('metakeys');
	$arMeta[$regname] = $metakeys;
	$db->set('metakeys', $arMeta);
	returnData(array('ok'));
}

function doGetMeta($project, $regname) {
	$db = getFlinstone($project);
	// TODO - нужно проверять то, что передали в $metakeys
	$arMeta = $db->get('metakeys');
	returnData($arMeta);
}

function doGetByRegname($project, $regname) {
	$db = getFlinstone($project);
	$arData = $db->get('data');
	returnData($arData[$regname]);
}

function doDropKey($project, $key) {
	$db = getFlinstone($project);
	$db->delete($key);
	returnData(array('ok'));
}

if($mode=='get') {
	$project = trim($_REQUEST['project']);
	$regname = trim($_REQUEST['regname']);
	$arKeys = $_REQUEST['keys'];
	doGet($project, $regname, $arKeys);
} elseif($mode=='set') {
	$project = trim($_REQUEST['project']);
	$regname = trim($_REQUEST['regname']);
	$arVals = $_REQUEST['vals'];
	doSet($project, $regname, $arVals);
} elseif($mode=='getkeys') {
	$project = trim($_REQUEST['project']);
	doGetKeys($project);
} elseif($mode=='setmeta') {
	$project = trim($_REQUEST['project']);
	$regname = trim($_REQUEST['regname']);
	$metakeys = $_REQUEST['metakeys'];
	doSetMeta($project, $regname, $metakeys);
} elseif($mode=='getmeta') {
	$project = trim($_REQUEST['project']);
	$regname = trim($_REQUEST['regname']);
	doGetMeta($project, $regname);

} elseif($mode=='getbyregname') {
	$project = trim($_REQUEST['project']);
	$regname = trim($_REQUEST['regname']);
	doGetByRegname($project, $regname);
} elseif($mode=='dropkey') {
	$project = trim($_REQUEST['project']);
	$key = trim($_REQUEST['key']);
	doDropKey($project, $key);
}





?>