#  WikiContributions II JS

## INF6150 A2013

INF6150 Génie logiciel: conduite de projets informatiques

## Équipe VOGG

Membres de l'équipe

* Victor Bitca
* Olivier Charrier
* Guillaume Lahaie
* Guy Francoeur

## Objectifs

* Une application http://ialex.ca/reconnaissance existait. Nous avons eu comme mandat de la modifier pour y ajouter des fonctionnalités.  Au lieu de cela mon avons opté pour une preuve de concept qui aurait pour but premier : regler un problème d'architecture conceptuelle et de vitesse. 

### Idée

* Il est plus simple et plus rapide d'utiliser les ordinateurs et le navigateur de chaque utilisateur pour faire les calculs et ainsi distribuer la charge au lieu de la garder dans le serveur php.

### Spécifique

* Refaire l'application original php de wikiContributions (GRISOU) en JavaScript, JQuery (Ajax)
* Donner un look et contruire un framework visuel éventuellement adaptable.
* Ajouter le retreive progressif des articles d'un contributeur.
* Ajouter le total score des article qui sont dans la liste (valeur absolut de size diff).
* Ajouter l'onglet Talk.
* Ajouter la distance d'édition (Levensthein distance).
* Ajouter la recherche avancée.
* Ajouter des informations sur les contributions:
  * la grosseur en char de l'article.
  * la difference en char entre l'article précédent et ma contribution.
  * Ajouter la date de la contribution dans cet article.

----------------------------------------------------------

# wikiContributions

wikiContributions is a tool to help scientists get recognition for their contributions to scientific wikis. We believe that eventually, scientific wikis will replace the conventional scientific articles published in expensive and non-collaborative magazines. When this will happen, it will be harder for a member of the scientific community to mention what articles he/she has contributed to in his/her resume, since wiki contributions are collaborative and can be anything from a comma to a whole article. We aim to provide a measurement tool for this, and our project is well under way. You can find information about both using wikiContributions and contributing to the project in the wiki help page : https://github.com/GrisouUQAM/wikiContributions/wiki/English. 
The code uses code provided by **Google-Diff, Match and Patch** for revision comparisons (found in the `google-diff` folder).

**If you wish to edit parts of this code, we will gladly add you as a contributor. All you need to do is write a comment in the [issue] (https://github.com/GrisouUQAM/wikiContributions/issues) you are interested in!**

Please note that documentation for this github depository has been inspired by the [CSS Lint page] (https://github.com/stubbornella/csslint), which is truly a model open-source project.

---------------------------------------------------------

# wikiContributions

wikiContributions est un outil visant à aider les scientifiques à obtenir de la reconnaissance pour leurs contributions aux wikis scientifiques. Nous croyons qu'éventuellement, les wikis scientifiques remplaceront les articles conventionnels publiés dans les revues scientifiques dispendieuses et non-collaboratives. Lorsque ce sera le cas, il sera plus difficile pour un membre de la communauté scientifique d'inscrire dans son CV quelles sont les contibutions qu'il/elle aura apportées à des articles sur internet, puisque celles-ci sont de nature collaborative, et peuvent aller de la virgule jusqu'à un article de 10 pages complet. Nous souhaitons ainsi fournir un outil de mesure, et notre projet va bon train. Vous pourrez trouver de l'information concernant la façon d'utiliser notre site web ainsi que la façon d'y contribuer sur la page d'aide wiki de notre dépôt:
https://github.com/GrisouUQAM/wikiContributions/wiki/Fran%C3%A7ais. 
Notre projet utilise le code fourni par **Google-Diff, Match and Patch** pour faire la comparaison de différentes révisions (que vous trouverez dans le dossier `google-diff`).

**Si vous souhaitez modifier des parties de ce code, nous serons heureux de vous ajouter à nos collaborateurs. Vous n'avez qu'à laisser un commentaire dans la [question/problème] (https://github.com/GrisouUQAM/wikiContributions/issues) qui vous intéresse!**

Prenez note que la documentation de ce dépôt github a été inspirée par la page de [CSS Lint] (https://github.com/stubbornella/csslint), qui est un vrai modèle de projet ouvert.

--------------------------------------------------------

## Membres GRISOU

### Initiateurs

* Robert Dupuis
* Anne Goldenberg
* Louise Laforest

### Contributeurs

* Laurence Loiselle Dupuis
* Melanie Lord
* Daniel Memmi
* Normand Seguin
* Nguyen Tho Hau
* Sylvie Trudel
