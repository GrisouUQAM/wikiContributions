<?php
$contributor = $_GET["user"];

$completeUrl = "http://" . explode('/',  $_GET["wiki"])[0];

require_once("config.php");
require_once("APIhelper.php");
require_once("contribution.php");
require_once("revision.php");
include_once( dirname(__FILE__) . "/diffFunctions.php");

function showGoogleDiff($text1, $text2) {
	return prettyHtml(getDiff($text1, $text2), strlen(utf8_decode($text1)));
}

// A user agent is required by MediaWiki API
ini_set('user_agent', $conf['user_agent']);

$usercontributions = contribution::fetch($contributor);

$result = '<h1>Articles which ' . $contributor . ' contributed to</h1>
            <table>
				<tr>            
					<th>Articles from ' . $completeUrl . '</th>
					<th>How long did the edit survive?</th>
					<th>Edits</th>
					<th>What is the value of the contribution?</th>					
				</tr>
				';
foreach ($usercontributions as $contribution) {
	$result .= '<tr><td>'.$contribution->title.'</td>';

	$analysisTable = showGoogleDiff($oldText, $newText);

	/////////////////////////// Timestamps comparisons /////////////////////////////////////				
	
	$oldVersionTimeUrl = $completeUrl."/w/api.php?action=query&prop=info&format=json&inprop=notificationtimestamp&revids=".$oldVersion."&converttitles=";
	$jsonSecondTimeQuery = file_get_contents($oldVersionTimeUrl, true);	
	$object = json_decode($jsonSecondTimeQuery, true);
	$queries = $object['query'];
	$pages = $queries['pages'];
	$revision = $pages[$pageId];
	$oldTimeStamp = $revision['touched'];

	$time1 = new DateTime($usertimestamp);
	$time2 = new DateTime($oldTimestamp);	

	$dateDifference = date_diff($time1, $time2);
	$timeDiffString = " ".$dateDifference->format('%D:%M:%S')." ";
	$result .= '<td>'.$timeDiffString.'</td>';
	$result .= '<td>'.$analysisTable.'</td>';
	$result .= '<td>Score quelconque</td></tr>';
}

$result .= '</table>
			<h2>Total score</h2>';

print $result;
