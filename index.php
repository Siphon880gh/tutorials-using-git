<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("git-php/Git.php");
$repo = Git::open('./');
?>

<h2>Tutorials Using Git Branches and Commits</h2>

<?php

$output = $repo->run("log --graph --abbrev-commit --decorate --exclude=refs/notes/commits --format=format:'%d: %s %C(bold blue)[%H]%C(reset)%n   %C(white)%an%C(reset) %C(dim white) - %aD (%ar)' --all");
$lines = explode("\n", $output);

echo "<main style='height:50vh;'><pre>"; // preserving tabs to HTML display
for($i = 0; $i<count($lines); $i++) {
    $line = $lines[$i];
    if($i%2===0) {
        $line = str_replace(" : ", "", $line); // Remove : . Because %d or branch name only appears when branching, otherwise shows : instead of (branchName) :
        echo sprintf("%s$line%s", "<span class='hover-notes' style='color:blue;'>", "</span><br>");
    } else {
        $isMatchAfterSymbol = preg_match('/[0-9A-Za-z]/', $line, $matches, PREG_OFFSET_CAPTURE);
        $strPos = $isMatchAfterSymbol ? $matches[0][1]:-1;
        if($strPos!==-1) {
            echo sprintf("%s<span style='color:lightgray;'>%s</span><br><br>", substr($line, 0, $strPos-1), substr($line, $strPos));
        }
    }
}
echo "</pre></main>"

?>

<aside id="selected"><br></aside>
<aside id="notes" style="border-top: 1px solid gray; padding-top: 5px;"></aside>

<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/showdown/1.3.0/showdown.min.js"></script>

<script>
$(function() {

    getHash = function(firstLine) { 
        return firstLine.substring(
                    firstLine.lastIndexOf("[") + 1, 
                    firstLine.lastIndexOf("]")
                );
    }; 

    getNoteByHash = function(hash) {
        $.getJSON("get-note.php", {hash: hash}, (objNote) => castNoteToMD(objNote.note));
    }

    // mouse enter
    $(".hover-notes").on("mouseenter", (e) => { 
        $("#selected").text(e.target.innerText);
        const hash = getHash(e.target.innerText);
        getNoteByHash(hash);
    });

    // markdown convert
    castNoteToMD = function(note) {
        var converter = new showdown.Converter();
        converter.setOption("literalMidWordUnderscores", true);
        $("body").html();
        $("#notes").html(converter.makeHtml(note));
        console.log(note);
    }

    // https://kbjr.github.io/Git.php/

    // git does not allow git log graph combined with --reverse
    // https://github.com/jonas/tig/issues/127
    // work around is piping to tac which is not included in all environments so that's prohibitive


    // Working on Notes
    // https://git-scm.com/docs/git-notes

    // Another view:
    // git show-branch
    

    /* Now this is a tip of master */
});    
</script>
    