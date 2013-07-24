<?php
include_once("\source\weha\WikiDiffFormatter.php");

function ShowDiff(){

//$myFile = "NewText.txt";
/*$fh = fopen($myFile, 'r');*/
//$newText = fread($fh, filesize($myFile));
$newText = "In computer science, a binary tree is a tree data structure in which each node has at most two child nodes, usually distinguished as 'left' and 'right'. Nodes with children are parent nodes, and child nodes may contain references to their parents. Outside the tree, there is often a reference to the 'root' node (the ancestor of all nodes), if it exists. Any node in the data structure can be reached by starting at root node and repeatedly following references to either the left or right child. A tree which does not have any node other than root node is called a null tree. In a binary tree, a degree of every node is maximum two. A tree with n nodes has exactly n−1 branches or degree.";
//fclose($fh);	

//$myFile = "OldText.txt";
//$fh = fopen($myFile, 'r');
//$oldText = fread($fh, filesize($myFile));
$oldText = "In computer science, a binary tree is a tree data structure in which each node has at most two child nodes, usually distinguished as 'left' and 'right'. In a binary tree, a degree of every node is maximum two. A tree with n nodes has exactly n−1 branches or degree. Nodes with children are parent nodes, and child nodes may contain references to their parents. Outside the tree, there is often a reference to the 'root' node (the ancestor of all nodes), if it exists. Any node in the data structure can be reached by starting at root node and repeatedly following references to either the left or right child. A tree which does not have any node other than root node is called a null tree.";
//fclose($fh);

$ac = new WikiDiffFormatter($oldText, $newText);
$result = $ac->outputDiff();

echo $result;

}

ShowDiff();