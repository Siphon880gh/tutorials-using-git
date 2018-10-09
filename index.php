<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("git-php/Git.php");
$repo = Git::open('./');

//echo exec("/usr/local/git/libexec/git-core/git log");

printf($repo->log() . "%s", "hi");
?>