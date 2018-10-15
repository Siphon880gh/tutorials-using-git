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
window.holdingShiftKey = false;

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

openAllFromBottom = function() {
    $("a.hover-notes").toArray().reverse().splice(1).forEach(function(el) { 
        const $el = $(el);
        const hash = $(el).data("hash");
        window.open(`deps/get-diff.php?current_hash=${hash}&title=${title}`, "_blank");
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
        if(window.holdingShiftKey)
            window.open(`deps/alt-git-diff/get-diff.php?current_hash=${hash}&title=${title}`, "_blank");
        else
            window.open(`deps/get-diff.php?current_hash=${hash}&title=${title}`, "_blank");
    });

    $("body").on("keydown", function(e) {
        if(e.shiftKey)
            window.holdingShiftKey = true;
    });
    $("body").on("keyup", function(e) {
        if(e.shiftKey)
            window.holdingShiftKey = false;
    });
});    
</script>

<style>
body {
    margin: 0 5px 0 5px;
}
#container-info {
    margin-top: 10px;
    margin-left: 10px;
    margin-right: 10px;
    /* border-width: 1px 0 0 1px;
    border-style: solid;
    border-color: rgba(125,125,125,0.7); */
    padding: 5px;
    border-radius: 5px;
    box-shadow: 30px 0 10px rgba(0,0,0,.1);
    height: 100%;
    overflow: 'hidden';
    transition: max-height 4s linear;
}
#notes, main {
    overflow-y: scroll;
}
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
::-webkit-scrollbar {
    -webkit-appearance: none;
    width: 7px;
}
::-webkit-scrollbar-thumb {
    border-radius: 4px;
    background-color: rgba(0,0,0,.5);
    -webkit-box-shadow: 0 0 1px rgba(255,255,255,.5);
}
/* Word-wrapping in pre */
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

<main style='height:50vh; overflow-y: scroll;'>
<h2>Tutorials Using Git Branches and Commits</h2>

    <div id="container-instructions">
        <label for="js-instructions-btn">Instructions:</label>&nbsp;<span id="js-instructions-btn" class="fa fa-toggle-off" onclick="toggleBtn($(this))" style="cursor:pointer;"></span>
        <div id="js-instructions" style="display:none;">
        <ol>
            <li><i>What is:</i> This is a walkthrough tutorial generator and reader for git repos by leveraging the power of git diff and git notes. You can get your team up to speed or personally review how to setup different boilerplates (like webpack). This tool show a list of branches and commits in order of creation from the <a href="#" onclick="$('main').animate({scrollTop: $('main')[0].scrollHeight},'medium');">bottom</a>. You may add notes to particular commits with the command git note. Those notes can have multiple lines and Markdown styles so that the walkthroughs look like formatted documents.</li>
            <li><i>About git notes:</i> Git does not by default fetch and push notes. Here's to do it manually or to setup defaults: <a href="#" data-toggle="modal" data-target="#pushing-fetching-notes">Pushing and Fetching Git Notes</a>.</li>
            <li><i>How to install:</i> Place start.php and /deps in any git repos. Run start.php on a PHP server.</li>
            <li><i>How to use:</i> Commits with the note icon <i class="fa fa-file-text-o"></i> have notes you can view by hovering the mouse cursor over. Click commits in sequential order to step through the code changes and view any notes. Hold shift while clicking a commit to view the alternate git diff that is side by side. <a href="#" onclick="openAllFromBottom();">Open all commit git diffs.</a><br></li>
            <li><i>Suggested use of branches:</i> You can use branches to show how the code could change at different points, for example, with different setups of webpack.</li>
        </ol>
    </div>
    <div id="container-commits">
        <pre><!-- pre: to show tab characters --><?php
            $output = $repo->run("log --graph --abbrev-commit --decorate --exclude=refs/notes/commits --format=format:'**[**%d: %s %C(bold blue)[%H]%C(reset)**]**%n   %C(white)%an%C(reset) %C(dim white) - %aD (%ar) --all'");
            $lines = explode("\n", $output);

            for($i = 0; $i<count($lines); $i++) {
                $line = $lines[$i];
                if(strpos($line, "**[**")!==false) {
                    $line = str_replace("**[**", "", $line);
                    $line = str_replace("**]**", "", $line);
                    if(strpos($line, " : ") === false) {
                        $beginBranchName = strpos($line, "(");
                        $endBranchName = strpos($line, ":")+1;
                        $line = substr($line, 0, $beginBranchName) . htmlentities(substr($line, $beginBranchName, $endBranchName-$beginBranchName)) . htmlentities(substr($line, $endBranchName));
                    } else
                        $line = htmlentities(str_replace(" : ", "", $line)); // Remove : . Because %d or branch name only appears when branching, otherwise shows : instead of (branchName):

                    $pos = strpos($line, "*"); // ; sometimes the line starts with | *
                    $line = substr($line, 0, $pos) . "<a class='hover-notes' data-hash='' data-title='' data-note=''>* " . substr($line, $pos+1) . "</a><br>";
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
    </div>
</main>

<aside id="container-info">
    <div id="indicator-current-commit" style="margin-top: 1rem;"><br></div>
    <div id="notes" style="border-top: 1px solid gray; padding-top: 5px;"></div>
</aside>

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


<h5>Portable git config</h5>

<p>You may want to create a ./.gitconfig file at the root to have the same configs in .git/config because git doesn't backup the .git folder<br><br>
To apply the configuration of .gitconfig, each user needs to run:
<code>git config --local include.path ../.gitconfig</code>
</p>


<h5>Lost Notes</h5>
<p>Renaming or rebasing commits will change the commit's ID. It also changes the ID's of downstream commits. This will cause the note to be lost. You'll want to revisit the dangling commits:<p></p>
<ol>
    <li><code class="inline">git fsck --lost-found</code></li>
    <li><code class="inline">git notes show COMMIT_ID</code></li>
    <li>Once you find the right notes, copy and paste it over.</li>
</ol>
</p>

<p style="font-size:.7em;"><br/>
From: <br><a href="https://gist.github.com/topheman/ec8cde7c54e24a785e52" target="_blank">https://gist.github.com/topheman/ec8cde7c54e24a785e52</a><br/>
<a href="https://stackoverflow.com/questions/18329621/storing-git-config-as-part-of-the-repository" target="_blank">https://stackoverflow.com/questions/18329621/storing-git-config-as-part-of-the-repository</a>
</p>
</div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div> <!-- Modal -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/showdown/1.3.0/showdown.min.js"></script>
</body>
</html>
