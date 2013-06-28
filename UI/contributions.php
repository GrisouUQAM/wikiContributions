<?php
$contributor = $_GET["user"];

include_once( dirname(__FILE__) . "/../source/phpfiles/weha/WikiDiffFormatter.php");

function ShowDiff($oldText, $newText){
	//$myFile = "NewText.txt";
	/*$fh = fopen($myFile, 'r');*/
	//$newText = fread($fh, filesize($myFile));
	//fclose($fh);	

	//$myFile = "OldText.txt";
	//$fh = fopen($myFile, 'r');
	//$oldText = fread($fh, filesize($myFile));
	//fclose($fh);

	$ac = new WikiDiffFormatter($oldText, $newText);
	$analysis = $ac->outputDiff();

	return $analysis;
}

$jsonurl = "http://fr.wikipedia.org/w/api.php?action=query&list=usercontribs&format=json&ucuser=".$contributor."&ucprop=ids%7Ctitle%7Ctitle&converttitles=";
$json = file_get_contents($jsonurl, true);

$obj = json_decode($json, true);

$queries = $obj['query'];
$usercontributions = $queries['usercontribs'];

$contribstring = "";
$timeDiffString = "";

$pageId = "";
$pages = "";
$userrevision = "";
$revision = "";
$prevContent = "";
$usertimestamp = "";
$oldTimestamp = "";
$oldVersion = "";
$userVersion = "";
$oldText = "";
$newText = "";
$analysisTable = "";
$result = "";

$test = "";

$result = '<h1>Articles which '.$contributor.' contributed to</h1>
            <table>
				<tr>            
					<th>Articles</th>
					<th>How long did the edit survive?</th>
					<th>Edits</th>
					<th>What is the value of the contribution?</th>					
				</tr>
				';

			
foreach ($usercontributions as $contribution) {
	$result .= '<tr><td>'.$contribution['title'].'</td>';
	$pageId = $contribution['pageid'];
	$revurl = "http://fr.wikipedia.org/w/api.php?action=query&prop=revisions&format=json&rvprop=ids%7Ctimestamp%7Cuser&rvuser=".$contributor."&pageids=".$pageId."";
	$json = file_get_contents($revurl, true);
	$obj = json_decode($json, true);	
	$queries = $obj['query'];
	$pages = $queries['pages'];
	$revision = $pages[$pageId];
	$userrevision = $revision['revisions'];
	foreach($userrevision as $temp) {
		$oldVersion = $temp['parentid'];
		$userVersion = $temp['revid'];
		$usertimestamp = $temp['timestamp'];
	}	
	
	$oldRevisionContent = "http://fr.wikipedia.org/w/api.php?action=parse&format=json&oldid=".$oldVersion."&prop=text";
	$jsonOld = file_get_contents($oldRevisionContent, true);
	$oldTextDecoded = json_decode($jsonOld, true);	
	$parsedOldText = $oldTextDecoded['parse'];
	$oldTextText = $parsedOldText['text'];
	$oldText = $oldTextText['*'];
	$userRevisionContent = "http://fr.wikipedia.org/w/api.php?action=parse&format=json&oldid=".$userVersion."&prop=text";
	$jsonNew = file_get_contents($userRevisionContent, true);
	$newTextDecoded = json_decode($jsonNew, true);	
	$parsedNewText = $newTextDecoded['parse'];
	$newTextText = $parsedNewText['text'];
	$newText = $newTextText['*'];
	//$analysisTable = "test";
	$analysisTable = ShowDiff($oldText, $newText);
	
	
	
	/////////////////////////// Timestamps comparisons /////////////////////////////////////				
	
	$oldVersionTimeUrl = "http://fr.wikipedia.org/w/api.php?action=query&prop=info&format=json&inprop=notificationtimestamp&revids=".$oldVersion."&converttitles=";
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


