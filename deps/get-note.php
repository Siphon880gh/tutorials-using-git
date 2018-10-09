<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("git-php/Git.php");
$repo = Git::open('../');
?>

<?php
    if(!isset($_GET["hash"]))
        echo json_encode(array("note"=>"ERROR: This app is coded wrong. No hash number sent."));
    else {
        try {
            $note = $repo->run(sprintf("notes show %s", $_GET["hash"]));
        } catch (Exception $e) {
            $note = "";
        }
        echo json_encode(array("note"=>$note));
    }
?>