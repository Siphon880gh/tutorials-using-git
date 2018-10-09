<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("git-php/Git.php");
$repo = Git::open('./');
?><!DOCTYPE html>
<html>
<head>
<title>Tutorials Using Git Branches and Commits</title>

<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script>
window.holdingSecretKey = false;
$(function() {

    //TODO: After rebasing or amending, the notes disappeared! How to preserve?

    // parse hash from line
    parseHash = function(firstLine) { 
        return firstLine.substring(
                    firstLine.lastIndexOf("[") + 1, 
                    firstLine.lastIndexOf("]")
                );
    }; 

    // get note by hash
    getNoteByHash = function(hash) {
        $.getJSON("get-note.php", {hash: hash}, (objNote) => castNoteToMD(objNote.note));
    }

    // cast note to markdown
    castNoteToMD = function(note) {
        var converter = new showdown.Converter();
        converter.setOption("literalMidWordUnderscores", true);
        $("body").html();
        $("#notes").html(converter.makeHtml(note));
        console.log(note);
    }

    // mouse enter
    $(".hover-notes").on("mouseenter", (e) => { 
        $("#selected").text(e.target.innerText);
        const hash = parseHash(e.target.innerText);
        getNoteByHash(hash);
    });


    // gitd
    $(".hover-notes").on("click", (e) => { 
        $("#selected").text(e.target.innerText);
        const hash = parseHash(e.target.innerText);
        if(window.holdingSecretKey)
            window.open(`alt-git-diff/get-diff.php?current_hash=${hash}`, "_blank");
        else
            window.open(`get-diff.php?current_hash=${hash}`, "_blank");
    });

    // https://kbjr.github.io/Git.php/

    // git does not allow git log graph combined with --reverse
    // https://github.com/jonas/tig/issues/127
    // work around is piping to tac which is not included in all environments so that's prohibitive


    // Working on Notes
    // https://git-scm.com/docs/git-notes

    // Another view:
    // git show-branch


    /* Now this is a tip of master */

    $("body").on("keydown", function(e) {
        if(e.shiftKey)
            window.holdingSecretKey = true;
    });
    $("body").on("keyup", function(e) {
        if(e.shiftKey)
            window.holdingSecretKey = false;
    });
});    

</script>

<style>
.hover-notes {
    cursor: pointer;
}
</style>

</head>
<body>

<main style='height:50vh; overflow-y: scroll;'>
<h2>Tutorials Using Git Branches and Commits</h2>
<p>1. Move mouse over commits for any notes you added with <i>git notes edit</i>, ideally where you write parts of the tutorial in Markdown format.<br>2. Click a commit for a git diff with the commit more upstream, ideally what the tutorial is referring to.<br>3. Hold shift while clicking a commit for the alternate git diff view that's side by side (draw back is there's no word wrapping).</p>
    <pre><!-- pre: to show tab characters --><?php
        $output = $repo->run("log --graph --abbrev-commit --decorate --exclude=refs/notes/commits --format=format:'%d: %s %C(bold blue)[%H]%C(reset)%n   %C(white)%an%C(reset) %C(dim white) - %aD (%ar)' --all");
        $lines = explode("\n", $output);

        for($i = 0; $i<count($lines); $i++) {
            $line = $lines[$i];
            if($i%2===0) {
                if(strpos($line, " : ") === false) {
                    $beginBranchName = strpos($line, "(");
                    $endBranchName = strpos($line, ":")+1;
                    $line = substr($line, 0, $beginBranchName) . "<span style='color:red;'>" . substr($line, $beginBranchName, $endBranchName-$beginBranchName) . "</span>" . substr($line, $endBranchName);
                } else
                    $line = str_replace(" : ", "", $line); // Remove : . Because %d or branch name only appears when branching, otherwise shows : instead of (branchName):

                $pos = strpos($line, "*"); // ; sometimes the line starts with | *
                $line = substr($line, 0, $pos) . "<span class='hover-notes' style='color:blue;'>" . substr($line, $pos) . "</span><br>";
                echo $line;
            } else {
                $isMatchAfterSymbol = preg_match('/[0-9A-Za-z]/', $line, $matches, PREG_OFFSET_CAPTURE);
                $strPos = $isMatchAfterSymbol ? $matches[0][1]:-1;
                if($strPos!==-1) {
                    echo sprintf("%s<span style='color:lightgray;'>%s</span><br><br>", substr($line, 0, $strPos-1), substr($line, $strPos));
                }
            }
        }
        ?>
    </pre>
</main>

<aside id="selected" style="margin-top: 1rem;"><br></aside>
<aside id="notes" style="border-top: 1px solid gray; padding-top: 5px;"></aside>

<script src="https://cdnjs.cloudflare.com/ajax/libs/showdown/1.3.0/showdown.min.js"></script>
</body>
</html>
    