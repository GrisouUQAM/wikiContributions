<?php
$contributor = $_GET["user"];
$wikiurl = $_GET["wiki"];

$withoutSlash = explode('/', $wikiurl);
$url = $withoutSlash[0];
$completeUrl = "http://";
$completeUrl.= $url;

include_once( dirname(__FILE__) . "/../source/weha/WikiDiffFormatter.php");

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

// A user agent is required by MediaWiki API
ini_set('user_agent', 'ProjetGrisou/1.1 (http://grisou.uqam.ca; grisou.science@gmail.com)');

$jsonurl = $completeUrl."/w/api.php?action=query&list=usercontribs&format=json&ucuser=".$contributor."&ucprop=ids%7Ctitle%7Ctitle&converttitles=";
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
					<th>Articles from '.$completeUrl.'</th>
					<th>How long did the edit survive?</th>
					<th>Edits</th>
					<th>What is the value of the contribution?</th>					
				</tr>
				';

			
foreach ($usercontributions as $contribution) {
	$result .= '<tr><td>'.$contribution['title'].'</td>';
	$pageId = $contribution['pageid']; //for Chris857 on fr.wikipedia.org, for example, there are 2 pageIds: 4330184 and 1532011
	$revurl = $completeUrl."/w/api.php?action=query&prop=revisions&format=json&rvprop=ids%7Ctimestamp%7Cuser&rvuser=".$contributor."&pageids=".$pageId."";
	$json = file_get_contents($revurl, true);
	$obj = json_decode($json, true);	
	$queries = $obj['query'];
	$pages = $queries['pages'];
	$revision = $pages[$pageId];
	$userrevision = $revision['revisions'];
	foreach($userrevision as $temp) {
		$oldVersion = $temp['parentid']; // for Chris857 on fr.wikipedia.org, the Rivière Nicolet article for example, the parentid is 73329365
		$userVersion = $temp['revid']; // for Chris857 on fr.wikipedia.org, the Rivière Nicolet article for example, the revid is 73631574
		$usertimestamp = $temp['timestamp'];
	}	
	
	$oldRevisionContent = $completeUrl."/w/api.php?action=parse&format=json&oldid=".$oldVersion."&prop=text";
	$jsonOld = file_get_contents($oldRevisionContent, true);
	$oldTextDecoded = json_decode($jsonOld, true);	
	$parsedOldText = $oldTextDecoded['parse'];
	$oldTextText = $parsedOldText['text'];
	$oldText = $oldTextText['*'];
	$userRevisionContent = $completeUrl."/w/api.php?action=parse&format=json&oldid=".$userVersion."&prop=text";
	$jsonNew = file_get_contents($userRevisionContent, true);
	$newTextDecoded = json_decode($jsonNew, true);	
	$parsedNewText = $newTextDecoded['parse'];
	$newTextText = $parsedNewText['text'];
	$newText = $newTextText['*'];
	//$analysisTable = "test";
	$analysisTable = ShowDiff($oldText, $newText);
	
	
	
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


