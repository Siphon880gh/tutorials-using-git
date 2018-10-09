<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("git-php/Git.php");
$repo = Git::open('./');

require_once 'git-diff/lib/DiffPage.php';
require_once 'git-diff/lib/DiffSection.php';
require_once 'git-diff/lib/DiffLine.php';

use ilovephp\DiffPage;
use ilovephp\DiffLine;
?>

<link href="git-diff/assets/sass/compiled.css" rel="stylesheet">

<?php
    $gitd = "";
    if(!isset($_GET["current_hash"]))
        echo json_encode(array("diff"=>"ERROR: This app is coded wrong. No current hash number sent."));
    else {
        try {
            $gitd = $repo->run(sprintf("diff %s^ %s", $_GET["current_hash"], $_GET["current_hash"]));
        } catch (Exception $e) {
            $gitd = "*No diff possible at this position.*";// . ": " . $e;
        }
    }

    // Analyse it here
    $gitDiff = new \ilovephp\DiffPage();
    $gitDiff->parseDiff($gitd);

    // Turn line numbers off if required (default = on)
    $gitDiff->setEnableLineNumbers(true);

    // Render it here
    $gitDiff->render();
?>

<div id="diff"></div>