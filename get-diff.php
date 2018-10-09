<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("git-php/Git.php");
$repo = Git::open('./');
?>

<?php
    $gitd = "";
    if(!isset($_GET["current_hash"]))
        echo json_encode(array("diff"=>"ERROR: This app is coded wrong. No current hash number sent."));
    else {
        try {
            $diff = $repo->run(sprintf("diff %s^ %s", $_GET["current_hash"], $_GET["current_hash"]));
            file_put_contents("temp.diff", $diff);
            exec("chmod +x temp.diff");
            $gitd = shell_exec("(cat ./temp.diff | ./diff2html.sh)");
            exec("rm temp.diff");
        } catch (Exception $e) {
            $gitd = "*No diff possible at this position.*";// . ": " . $e;
        }
    }

    echo $gitd;
?>

<div id="diff"></div>