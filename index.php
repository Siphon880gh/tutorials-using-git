<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("git-php/Git.php");
$repo = Git::open('./');

//$output = $repo->run("log --graph --abbrev-commit --decorate --format=format:'%s %C(bold blue)[%H]%C(reset)%n  %C(white)%an%C(reset) %C(dim white) - %aD (%ar)%d%n' --all");
$output = $repo->run("log --graph --abbrev-commit --decorate --format=format:'%s %C(bold blue)[%H]%C(reset)%n   %C(white)%an%C(reset) %C(dim white) - %aD (%ar)%d%N' --all");
$lines = explode("\n", $output);

echo "<pre>"; // preserving tabs to HTML display
for($i = 0; $i<count($lines); $i++) {
    $line = $lines[$i];
    if($i%2===0) {
        echo sprintf("%s$line%s", "<span style='color:blue;'>", "</span><br>");
    } else {
        $isMatchAfterSymbol = preg_match('/[0-9A-Za-z]/', $line, $matches, PREG_OFFSET_CAPTURE);
        $strPos = $isMatchAfterSymbol ? $matches[0][1]:-1;
        if($strPos!==-1) {
            echo sprintf("%s<span style='color:lightgray;'>%s</span><br>", substr($line, 0, $strPos-1), substr($line, $strPos));
        }
    }
}
echo "</pre>"

// Working on Notes
// https://git-scm.com/docs/git-notes

// Another view:
// git show-branch
 

/* Now this is a tip of master */
?>