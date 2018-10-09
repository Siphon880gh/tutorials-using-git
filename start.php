<?php
/* tutorials-using-git
 * https://github.com/Siphon880gh/tutorials-using-git
 * 
 * By Weng Fei Fung
 * MIT
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("deps/git-php/Git.php");
$repo = Git::open('./');
?><!DOCTYPE html>
<html>
<head>
<title>Tutorials Using Git Branches and Commits</title>

<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

<script>
window.holdingSecretKey = false;

// parse hash from line
parseHash = function(firstLine) { 
    return firstLine.substring(
                firstLine.lastIndexOf("[") + 1, 
                firstLine.lastIndexOf("]")
            );
}; 

// get note by hash
storeNoteByHash = function(hash, $commit) {
    $.getJSON("deps/get-note.php", {hash: hash}, (objNote) => {
        if(objNote.note.length) {
            $commit.data("note", objNote.note);
            $commit.append(`<i class="fa fa-file-text-o"></i>`);
        } else
            $commit.data("note", "");
    });
}


// cast note to markdown
castNoteToMD = function($commit) {
    const note = typeof $commit.data("note")==="undefined"?"":$commit.data("note");
    var converter = new showdown.Converter();
    converter.setOption("literalMidWordUnderscores", true);
    $("body").html();
    if(note.length)
        $("#notes").html(converter.makeHtml(note));
    else
        $("#notes").html("<i>No notes</i>");
}

$(function() {

    //TODO: After rebasing or amending, the notes disappeared! How to preserve?

    // get notes for every commit and store locally
    $(".hover-notes").each((i,el)=> {
        const hash = parseHash($(el).text());
        storeNoteByHash(hash, $(el));
    });

    // mouse enter
    $(".hover-notes").on("mouseenter", (e) => { 
        $("#indicator-current-commit").text(e.target.innerText);
        castNoteToMD($(e.target))
    });


    // click to see git diff (hold shift for alternate view)
    $(".hover-notes").on("click", (e) => { 
        $("#indicator-current-commit").text(e.target.innerText);
        const hash = parseHash(e.target.innerText);
        if(window.holdingSecretKey)
            window.open(`deps/alt-git-diff/get-diff.php?current_hash=${hash}`, "_blank");
        else
            window.open(`deps/get-diff.php?current_hash=${hash}`, "_blank");
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
1. Here's a tree of all the major steps for the app/boilerplate/bundler/etc.<br>
2. Start from the bottom of the tree and click the topic. Each topic is cumulative and dependent on the previous topic.<br>
3. You'll see what new codes there are from the previous step. Try to mimic these steps.<br>
4. And move your mouse over the step to see the tutorial for that step. If it says there are No Notes, then the step is likely self explanatory and the author did not add any instructions.<br><br>
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
                $line = substr($line, 0, $pos) . "<span class='hover-notes' style='color:blue;' data-note=''>" . substr($line, $pos) . "</span><br>";
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

<aside id="indicator-current-commit" style="margin-top: 1rem;"><br></aside>
<aside id="notes" style="border-top: 1px solid gray; padding-top: 5px;"></aside>

<script src="https://cdnjs.cloudflare.com/ajax/libs/showdown/1.3.0/showdown.min.js"></script>
</body>
</html>