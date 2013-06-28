<?php
# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if (!defined('MEDIAWIKI')) {
	echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/EditAnalysis/EditAnalysis.php" );
EOT;
	exit( 1 );
}

$wgExtensionCredits['other'][] = array(
	'name' => 'EditHistoryAnalyzer',
	'author' => 'Peter K. F. Fong',
	'url' => 'http://weha.sourceforge.net/',
	'description' => 'Provide wiki-syntax aware analysis of edit history',
	'descriptionmsg' => 'weha_desc',
	'version' => '0.0.1',
);

$dir = dirname(__FILE__) . '/'; # store the location of the setup file.
$wgAutoloadClasses['EditHistoryAnalyzer'] = $dir . 'EditHistoryAnalyzer.body.php'; # Tell MediaWiki where the extension class is.
$wgExtensionFunctions[] = 'EditHistoryAnalyzer::setup'; # Do all initial setup here.
$wgExtensionMessagesFiles['EditHistoryAnalyzer'] = $dir . 'EditHistoryAnalyzer.i18n.php';
