<?php

/**
 * Deplacement
 * 
 * Processus de détection des déplacements dans le texte
 * 
 * @author   JAVUM
 * @author   - Valentin Malissen 
 * @author   - Maxime Turmel
 * @author   - Alexandra Lapointe-Boisvert
 * @author   - Julien Tremblay
 * @author   - Ulysse Oriol
 * 
 */
define("TAUX_ACEPTABLE", 80);

class Deplacement {

    private $_donnees;
    private $_resultat;
    private $_tabSupp;
    private $_tabAjout;

    /**
     * Constructeur
     * 
     * @param Array $donnees
     */
    public function __construct($donnees) {
        $this->_donnees = $donnees;
        $this->_tabSupp = array();
        $this->_tabAjout = array();
        $this->_adapterDonnees();
        $this->_comparerSuppAjout();
        $this->_genererResultat();
    }

    /**
     * _adapterDonnees
     *
     * Permet de séparer les éléments ajouté et supprimé.
     * 
     * @access private
     */
    private function _adapterDonnees() {
        $d = $this->_donnees;
        for ($i = 0; $i < count($d); $i++) {
            $morceau = new MorceauTexte($d[$i][1], $i);
            if ($d[$i][0] == -1) {
                array_push($this->_tabSupp, $morceau);
            } elseif ($d[$i][0] == 1) {
                array_push($this->_tabAjout, $morceau);
            }
        }
    }

    /**
     * _comparerSuppAjout
     *
     * Permet de savoir quel élément à été déplacé selon un pourcentage acceptable.
     * 
     * @access private
     */
    private function _comparerSuppAjout() {
        $ajout = $this->_tabAjout;
        $supp = $this->_tabSupp;
        $tabRes = array();

        for ($i = 0; $i < count($ajout); $i++) {
            for ($j = 0; $j < count($supp); $j++) {
                $pour = $this->getLCS($ajout[$i]->getTexte(), $supp[$j]->getTexte());
                if ($pour >= TAUX_ACEPTABLE) {
                    array_push($tabRes, new Resultat($pour, $i, $j));
                }
            }
        }
        usort($tabRes, "_comparerResultat");

        foreach ($tabRes as $r) {
            if (!$ajout[$r->getIndAjout()]->estDeplacement() && !$supp[$r->getIndSupp()]->estDeplacement()) {
                $ajout[$r->getIndAjout()]->setEstDeplacement(True);
                $supp[$r->getIndSupp()]->setEstDeplacement(True);
                $ajout[$r->getIndAjout()]->setIndexDeplacement($supp[$r->getIndSupp()]->getIndex());
                $supp[$r->getIndSupp()]->setIndexDeplacement($ajout[$r->getIndAjout()]->getIndex());
            }
        }
    }

    /**
     * _genererResultat
     *
     * Permet de générer les données traitables par l'interface personne machine.
     * 
     * @access private
     */
    private function _genererResultat() {
        $ajout = $this->_tabAjout;
        $supp = $this->_tabSupp;
        $objArray = new ArrayObject($this->_donnees);
        $this->_resultat = $objArray->getArrayCopy();
        foreach ($ajout as $a) {
            if ($a->estDeplacement()) {
                $this->_resultat[$a->getIndex()][0] = 2;
                array_push($this->_resultat[$a->getIndex()], $a->getIndexDeplacement());
            }
        }
        foreach ($supp as $s) {
            if ($s->estDeplacement()) {
                $this->_resultat[$s->getIndex()][0] = -2;
                array_push($this->_resultat[$s->getIndex()], $s->getIndexDeplacement());
            }
        }
    }

    /**
     * getDeplacement
     *
     * Retourne le tableau pour l'interface personne machine.
     * @access public
     *
     * @return mixed Value.
     */
    public function getDeplacement() {
        return $this->_resultat;
    }

    /**
     * LCS_Length
     *
     * From: http://www.php.net/manual/en/function.similar-text.php#19734
     * 
     * @param string $s1
     * @param string $s2
     *
     * @access private
     *
     * @return Int Longest Common Sub sequense
     */
    private function LCS_Length($s1, $s2) {
        $m = strlen($s1);
        $n = strlen($s2);

        //this table will be used to compute the LCS-Length, only 128 chars per string are considered 
        $LCS_Length_Table = array(array(128), array(128));


        //reset the 2 cols in the table 
        for ($i = 1; $i < $m; $i++)
            $LCS_Length_Table[$i][0] = 0;
        for ($j = 0; $j < $n; $j++)
            $LCS_Length_Table[0][$j] = 0;

        for ($i = 1; $i <= $m; $i++) {
            for ($j = 1; $j <= $n; $j++) {
                if ($s1[$i - 1] == $s2[$j - 1]) {
                    $LCS_Length_Table[$i][$j] = $LCS_Length_Table[$i - 1][$j - 1] + 1;
                } else if ($LCS_Length_Table[$i - 1][$j] >= $LCS_Length_Table[$i][$j - 1]) {
                    $LCS_Length_Table[$i][$j] = $LCS_Length_Table[$i - 1][$j];
                } else {
                    $LCS_Length_Table[$i][$j] = $LCS_Length_Table[$i][$j - 1];
                }
            }
        }
        return $LCS_Length_Table[$m][$n];
    }

    /**
     * getLCS
     * 
     * @param String $texte1
     * @param String $texte2
     *
     * @access public
     *
     * @return Float Pourcentage de similitude
     */
    function getLCS($texte1, $texte2) {
        //ok, now replace all spaces with nothing 
        $texte1 = strtolower($texte1);
        $texte2 = strtolower($texte2);

        $lcs = $this->LCS_Length($texte1, $texte2); //longest common sub sequence 
        $ms = (strlen($texte1) + strlen($texte2)) / 2;

        return (($lcs * 100) / $ms);
    }

}


class MorceauTexte {

    private $_texte;
    private $_index;
    private $_estDeplacement;
    private $_indexDeplacement;

    function __construct($texte, $index) {
        $this->_texte = $texte;
        $this->_index = $index;
        $this->_estDeplacement = False;
    }

    public function getTexte() {
        return $this->_texte;
    }

    public function getIndex() {
        return $this->_index;
    }

    public function estDeplacement() {
        return $this->_estDeplacement;
    }

    public function setTexte($texte) {
        $this->_texte = $texte;
    }

    public function setIndex($index) {
        $this->_index = $index;
    }

    public function setEstDeplacement($estDeplacement) {
        $this->_estDeplacement = $estDeplacement;
    }

    public function getIndexDeplacement() {
        return $this->_indexDeplacement;
    }

    public function setIndexDeplacement($indexDeplacement) {
        $this->_indexDeplacement = $indexDeplacement;
    }

}

class Resultat {

    private $_pourcent;
    private $_indAjout;
    private $_indSupp;

    function __construct($_pourcent, $_indAjout, $_indSupp) {
        $this->_pourcent = $_pourcent;
        $this->_indAjout = $_indAjout;
        $this->_indSupp = $_indSupp;
    }

    public function getPourcent() {
        return $this->_pourcent;
    }

    public function getIndAjout() {
        return $this->_indAjout;
    }

    public function getIndSupp() {
        return $this->_indSupp;
    }

}

    /**
     * _comparerResultat
     * 
     * @param Resultat $a
     * @param Resultat $b
     *
     * @access private
     *
     * @return Float Positif / Negatif / Zero
     */
    function _comparerResultat($a, $b) {
        return $a->getPourcent() - $b->getPourcent();
    }

?>
