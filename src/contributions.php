<?php
$contributor = $_GET["user"];
$wikiurl = $_GET["wiki"];

$withoutSlash = explode('/', $wikiurl);
$url = $withoutSlash[0];
$completeUrl = "http://";
$completeUrl.= $url;

include_once( dirname(__FILE__) . '/diffFunctions.php');

function showGoogleDiff($text1, $text2) {
	$result = getDiff($text1, $text2); //Return an array of Diff objects
	$output = prettyHtml($result, strlen(utf8_decode($text1)));
	return $output;
}

// A user agent is required by MediaWiki API
//ini_set('user_agent', 'ProjetGrisou/1.1 (http://grisou.uqam.ca; grisou.science@gmail.com)');


///////////////////////////////////////////////////////Articles//////////////////////////////////////////////////////////////////////////////////////////
$jsonurl = $completeUrl."/w/api.php?action=query&list=usercontribs&format=json&ucuser=".$contributor."&ucnamespace=0%7C4%7C6%7C8&ucprop=ids%7Ctitle%7Ctitle&converttitles=";
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

$result = '<h1>Articles which '.$contributor.' contributed to</h1>
            <table>
				<tr>            
					<th>Articles from '.$completeUrl.'</th>
					<th>Has the contribution survived?</th>
					<th>Edits</th>
					<th>What is the value of the contribution?</th>					
				</tr>';
				
foreach ($usercontributions as $contribution) {
	$result .= '<tr><td>'.$contribution['title'].'</td>';
	$pageId = $contribution['pageid'];
	$revurl = $completeUrl."/w/api.php?action=query&prop=revisions&format=json&rvprop=ids%7Ctimestamp%7Cuser&rvuser=".$contributor."&pageids=".$pageId."";
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
	$analysisTable = showGoogleDiff($oldText, $newText);
	
	/////////////////////////// Does the contribution survive? ///////////////////////////////////
        
        //Return the lastest revision of an article
        $lastestRevisionQueryString = $completeUrl.'/w/api.php?action=query&prop=revisions&format=json&rvprop=ids%7Ctimestamp%7Cuser%7Cuserid%7Ccontent&rvlimit=1&rvdir=older&rvparse=&pageids='.$pageId;
        
        $lastestRevisionJson = file_get_contents($lastestRevisionQueryString,true);
        $lastestRevisionDecoded = json_decode($lastestRevisionJson,true);
        
        //JSON Obj Path: query:pages:$pageId:revisions[0] <-- only if the pageid exists
        $lastestRevisionProps = $lastestRevisionDecoded['query']['pages'][$pageId]["revisions"][0];
        
        $lastestRevisionId = $lastestRevisionProps["revid"];
        $lastestRevisionTimestamp = new DateTime($lastestRevisionProps["timestamp"]);
        $lastestRevisionContent = $lastestRevisionProps['*'];
        
        $survive = FALSE;
        
        if($userVersion === $lastestRevisionId){
            
        } else {
            //$result.='<td>'.'yes/no'.'</td>';
            $diff_result = getDiff($newText, $lastestRevisionContent);
            for($i = 0; $i<sizeof($diff_result);++$i)
                switch ($diff_result[$i][0]){
                    case 1: //If an insertion, check if still exists
                        $survive = getMatch($lastestRevisionContent, $diff_result[$i][1]);
                        break;
                    default:
                        continue;
            }
            if($survive) break;
        }
        
        if($survive){
            $result.= '<td>'.'Yes'.'</td>';
        } else {
            $result.= '<td>'.'No'.'</td>';
        }
	
        //Replaced by: Does the contribution survive?
	/////////////////////////// Timestamps comparisons on articles/////////////////////////////////////				
	
//	$oldVersionTimeUrl = $completeUrl."/w/api.php?action=query&prop=info&format=json&inprop=notificationtimestamp&revids=".$oldVersion."&converttitles=";
//	$jsonSecondTimeQuery = file_get_contents($oldVersionTimeUrl, true);	
//	$object = json_decode($jsonSecondTimeQuery, true);
//	$queries = $object['query'];
//	$pages = $queries['pages'];
//	$revision = $pages[$pageId];
//	$oldTimeStamp = $revision['touched'];
//
//	$time1 = new DateTime($usertimestamp);
//	$time2 = new DateTime($oldTimestamp);	
//
//	$dateDifference = date_diff($time1, $time2);
//	$timeDiffString = " ".$dateDifference->format('%D:%M:%S')." ";
//	$result .= '<td>'.$timeDiffString.'</td>';
        
        
	$result .= '<td>'.$analysisTable.'</td>';
	$result .= '<td>Score quelconque</td></tr>';
}

$result .= '</table>
			<h2>Total score</h2>
			<br/>
			<br/>';
			
////////////////////////////////////////////////////////////Talk////////////////////////////////////////////////////////////////////////////////
$jsonurlTalk = $completeUrl."/w/api.php?action=query&list=usercontribs&format=json&ucuser=".$contributor."&ucnamespace=1%7C3%7C5%7C9&ucprop=ids%7Ctitle%7Ccomment&converttitles=";
$jsonTalk = file_get_contents($jsonurlTalk, true);

$objTalk = json_decode($jsonTalk, true);

$queriesTalk = $objTalk['query'];
$userTalks = $queriesTalk['usercontribs'];

$result .= '<h1>Talks which '.$contributor.' contributed to</h1>
            <table>
				<tr>            
					<th>Articles talked about</th>
					<th>Title of the contribution-talk (topic)</th>				
				</tr>';
				
foreach ($userTalks as $talk) {
	$result .= '<tr><td>'.$talk['title'].'</td>';

	$result .= '<td>'.$talk['comment'].'</td></tr>';
}

$result .= '</table>
			<h2>Total score</h2>';

print $result;


