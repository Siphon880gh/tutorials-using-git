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
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

<script>
window.holdingSecretKey = false;

openAllFromBottom = function() {
    $("a.hover-notes").each((i,el)=> {
        const hash = $(el).data("hash");
        window.open(`deps/get-diff.php?current_hash=${hash}`, "_blank");
    });
}

// parse hash from line
parseHash = function(firstLine) { 
    return firstLine.substring(
                firstLine.lastIndexOf("[") + 1, 
                firstLine.lastIndexOf("]")
            );
}; 

parseTitle = function(firstLine) {
    const title = firstLine.substr(firstLine.indexOf("*")+1, firstLine.indexOf("[") - firstLine.indexOf("*")).trim();
    return title;
}

// store data-note
storeDataNote = function(hash, $commit) {
    $.getJSON("deps/get-note.php", {hash: hash}, (objNote) => {
        if(objNote.note.length) {
            $commit.data("note", objNote.note);
            $commit.append(`<i class="fa fa-file-text-o"></i>`);
        } else
            $commit.data("note", "");
    });
}

// cast note to markdown
castNoteToMD = function(note) {
    var converter = new showdown.Converter();
    converter.setOption("literalMidWordUnderscores", true);
    if(typeof note!=="undefined" && note.length)
        $("#notes").html(converter.makeHtml(note));
    else
        $("#notes").html("<i>No notes</i>");
}

$(function() {

    //TODO: After rebasing or amending, the notes disappeared! How to preserve?

    // get notes for every commit and store locally
    $("a.hover-notes").each((i,el)=> {
        const firstLine = $(el).text();
        const hash = parseHash(firstLine);
        const title = parseTitle(firstLine);
        $(el).data("hash", hash);
        $(el).data("title", title);
        storeDataNote(hash, $(el));
    });

    // mouse enter
    $("a.hover-notes").on("mouseenter", (e) => { 
        const firstLine = e.target.innerText;
        const note = $(e.target).data("note");
        $("#indicator-current-commit").text(firstLine);
        castNoteToMD(note);
    });
    $(".branch-name").on("mouseenter", (e) => {
        const $el = $(e.target),
              $a = $el.parent("a");
        debugger;
        $a.trigger("mouseenter");
    });

    // click to see git diff (hold shift for alternate view)
    $("a.hover-notes").on("click", (e) => { 
        const firstLine = e.target.innerText;
        const $el = $(e.target);
        $("#indicator-current-commit").text(firstLine);
        const hash = $el.data("hash");
        const title = $el.data("title");
        if(window.holdingSecretKey)
            window.open(`deps/alt-git-diff/get-diff.php?current_hash=${hash}&title=${title}`, "_blank");
        else
            window.open(`deps/get-diff.php?current_hash=${hash}&title=${title}`, "_blank");
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
a {
    color: blue;
}
a:active {
    color: red;
}
a:hover {
    color: red;
}
#notes, main {
    overflow-y: scroll;
}
::-webkit-scrollbar {
    -webkit-appearance: none;
    width: 7px;
}
::-webkit-scrollbar-thumb {
    border-radius: 4px;
    background-color: rgba(0,0,0,.5);
    -webkit-box-shadow: 0 0 1px rgba(255,255,255,.5);
}
pre {
    white-space: pre-wrap;       /* Since CSS 2.1 */
    white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
    white-space: -pre-wrap;      /* Opera 4-6 */
    white-space: -o-pre-wrap;    /* Opera 7 */
    word-wrap: break-word;       /* Internet Explorer 5.5+ */
}
</style>

</head>
<body>

<!-- Modal -->
<div class="modal fade" id="pushing-fetching-notes" tabindex="-1" role="dialog" aria-labelledby="pushing-fetching-notes-label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Pushing and Fetching Git Notes</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position:absolute; top:5px; right:5px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <h5>Push</h5>
        <p>Like tags, notes aren't pushed by default.</p>
        <pre><code>git push origin refs/notes/commits
git push origin "refs/notes/*"
        </code></pre>
        <h5>Fetch</h5>
        <p>Notes aren't fetched by default.</p>
        <pre><code>git fetch origin refs/notes/commits:refs/notes/commits
git fetch origin "refs/notes/*:refs/notes/*"
        </code></pre>
        <p>To fetch notes by default : <code>vi .git/config</code></p>
        <pre><code>#edit this part

[remote "origin"]
  fetch = +refs/heads/*:refs/remotes/origin/*

#to become

[remote "origin"]
  fetch = +refs/heads/*:refs/remotes/origin/*
  fetch = +refs/notes/*:refs/notes/*
</code></pre>

<p>You may want to create a ./.gitconfig file at the root to have the same configs in .git/config because git doesn't backup the .git folder<br><br>
To apply the configuration of .gitconfig, each user needs to run:
<code>git config --local include.path ../.gitconfig</code>
</p>

      <p><br/>
        From: <br><a href="https://gist.github.com/topheman/ec8cde7c54e24a785e52" target="_blank">https://gist.github.com/topheman/ec8cde7c54e24a785e52</a><br/>
        <a href="https://stackoverflow.com/questions/18329621/storing-git-config-as-part-of-the-repository" target="_blank">https://stackoverflow.com/questions/18329621/storing-git-config-as-part-of-the-repository</a>
      </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<main style='height:50vh; overflow-y: scroll;'>
<h2>Tutorials Using Git Branches and Commits</h2>
<script>
function toggleBtn($el) {
    if($el.hasClass('fa-toggle-on')) { 
        $el.removeClass('fa-toggle-on');
        $el.addClass('fa-toggle-off');
        $("#js-instructions").hide();
    } else {
        $el.removeClass('fa-toggle-off');
        $el.addClass('fa-toggle-on');
        $("#js-instructions").show();
    }
} // toggleBtn
</script>
<span class="fa fa-toggle-on" onclick="toggleBtn($(this))" style="cursor:pointer;"></span>
<div id="js-instructions">
1. The idea is a new platform for creating programming tutorials by taking advantage of git diff to see code changes from step to step and git notes that explain the steps. This tool will let you see git diff and notes in one place. Git notes is versatile because you can enter multiple lines of explanations and push them to the github repos (<a href="#" data-toggle="modal" data-target="#pushing-fetching-notes">though by default, pushing does not include notes</a>). Your git notes can have markdown and this page will effortlessly format the various headings, lists, emphasis, etc.<br>
2. The commits that are actually steps of the code changes start from the bottom. You see notes by hovering the mouse over. You see the code changes of the previous commit and current commit by clicking the commit. <a href="#" onclick="openAllFromBottom();">Open all from bottom.</a><br>
2b. Hold shift while clicking a commit for the alternate git diff view that's side by side (draw back is there's no word wrapping. Maybe in a future version this would be the newer git diff with word wrapping).<br>
3. How to port to other git repos: Download their repo. Then place start.php and /deps there. Run start.php on a server and you will see the git commits step by step.

</div>
<p/>
    <pre><!-- pre: to show tab characters --><?php
        $output = $repo->run("log --graph --abbrev-commit --decorate --exclude=refs/notes/commits --format=format:'%d: %s %C(bold blue)[%H]%C(reset)%n   %C(white)%an%C(reset) %C(dim white) - %aD (%ar)' --all");
        $lines = explode("\n", $output);

        for($i = 0; $i<count($lines); $i++) {
            $line = $lines[$i];
            if($i%2===0) {
                if(strpos($line, " : ") === false) {
                    $beginBranchName = strpos($line, "(");
                    $endBranchName = strpos($line, ":")+1;
                    $line = substr($line, 0, $beginBranchName) . htmlentities(substr($line, $beginBranchName, $endBranchName-$beginBranchName)) . htmlentities(substr($line, $endBranchName));
                } else
                    $line = htmlentities(str_replace(" : ", "", $line)); // Remove : . Because %d or branch name only appears when branching, otherwise shows : instead of (branchName):

                $pos = strpos($line, "*"); // ; sometimes the line starts with | *
                $line = substr($line, 0, $pos) . "<a class='hover-notes' data-hash='' data-title='' data-note=''>" . substr($line, $pos) . "</a><br>";
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