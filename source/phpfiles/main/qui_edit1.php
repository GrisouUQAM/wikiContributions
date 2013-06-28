<?php

/*
  CE FICHIER A ETE BOUGE VERS yolo/contributions/main/qui_edit.php
  Effectue une s�rie de tests.
 */

include("../classes/traitementRequetes/requeteur.class.php");
ini_set('user_agent', 'ProjetWiki (https://github.com/yolo-hipster/yolo-hipster; rlamour2@yahoo.ca)'); //Requis pour �viter une erreur 403
$skipper = "<br>";

function afficherUtilisateurs() {
    $url = $_GET['url'];
    $article = $_GET['article'];

    $wikiobject = ArticleWiki::createByWikiArticle($url, $article);



    $content = Requeteur::getUsers($wikiobject);

    $trouveMoi = "\"user\":";
    $nb = substr_count($content, $trouveMoi);
    $tableauUser = array(array());

    for ($i = 0; $i < $nb; $i++) {
        $position = strpos($content, $trouveMoi);
        $content = substr($content, $position + 8);
        $tailleTrouve = strpos($content, "\"");
        $user = substr($content, 0, $tailleTrouve);



        $key = array_search($user, $tableauUser[0]);
        if ($key) {
            $tableauUser[1][$key] += 1;
        } else {
            $count = count($tableauUser[0]);
            $tableauUser[0][$count] = $user;
            $tableauUser[1][$count] = 1;
        }
    }

    return $tableauUser;
}

$unTableau = afficherUtilisateurs();
$nb = count($unTableau[0]);

echo '{';
for ($i = 0; $i < $nb; $i++) {

    echo "{";
    echo '"Id" :' . $nb . ",";
    echo '"UserName":"' . $unTableau[0][$i] . '",';
    echo '"Number":' . $unTableau[1][$i];
    echo "}";
    if ($i < $nb - 1) {
        echo ",";
    }
}
echo "}";
?>