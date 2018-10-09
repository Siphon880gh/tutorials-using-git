diff --git a/get-note.php b/get-note.php
index 1752dcf..119d9c7 100644
--- a/get-note.php
+++ b/get-note.php
@@ -9,7 +9,11 @@ $repo = Git::open('./');
     if(!isset($_GET["hash"]))
         echo json_encode(array("note"=>"ERROR: This app is coded wrong. No hash number sent."));
     else {
-        $note = $repo->run(sprintf("notes show %s", $_GET["hash"]));
+        try {
+            $note = $repo->run(sprintf("notes show %s", $_GET["hash"]));
+        } catch (Exception $e) {
+            $note = "*No notes*";// . ": " . $e;
+        }
         echo json_encode(array("note"=>$note));
     }
 ?>
\ No newline at end of file
diff --git a/index.php b/index.php
index 2638165..89e768a 100644
--- a/index.php
+++ b/index.php
@@ -12,7 +12,7 @@ $repo = Git::open('./');
 $output = $repo->run("log --graph --abbrev-commit --decorate --exclude=refs/notes/commits --format=format:'%d: %s %C(bold blue)[%H]%C(reset)%n   %C(white)%an%C(reset) %C(dim white) - %aD (%ar)' --all");
 $lines = explode("\n", $output);
 
-echo "<main><pre>"; // preserving tabs to HTML display
+echo "<main style='height:50vh;'><pre>"; // preserving tabs to HTML display
 for($i = 0; $i<count($lines); $i++) {
     $line = $lines[$i];
     if($i%2===0) {
@@ -28,18 +28,10 @@ for($i = 0; $i<count($lines); $i++) {
 }
 echo "</pre></main>"
 
-// Working on Notes
-// https://git-scm.com/docs/git-notes
-
-// Another view:
-// git show-branch
- 
-
-/* Now this is a tip of master */
 ?>
 
 <aside id="selected"><br></aside>
-<aside id="notes" style="border-top: 1px solid gray; padding-top: 15px;"></aside>
+<aside id="notes" style="border-top: 1px solid gray; padding-top: 5px;"></aside>
 
 <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/showdown/1.3.0/showdown.min.js"></script>
@@ -74,7 +66,21 @@ $(function() {
         console.log(note);
     }
 
+    // https://kbjr.github.io/Git.php/
+
+    // git does not allow git log graph combined with --reverse
+    // https://github.com/jonas/tig/issues/127
+    // work around is piping to tac which is not included in all environments so that's prohibitive
+
+
+    // Working on Notes
+    // https://git-scm.com/docs/git-notes
+
+    // Another view:
+    // git show-branch
+    
 
+    /* Now this is a tip of master */
 });    
 </script>
     
\ No newline at end of file
#!/bin/bash
#
# Convert diff output to colorized HTML.

cat <<XX
<html>
<head>
<title>Colorized Diff</title>
</head>
<style>
.diffdiv  { border: solid 1px black;           }
.comment  { color: gray;                       }
.diff     { color: #8A2BE2;                    }
.minus3   { color: blue;                       }
.plus3    { color: maroon;                     }
.at2      { color: lime;                       }
.plus     { color: green; background: #E7E7E7; }
.minus    { color: red;   background: #D7D7D7; }
.only     { color: purple;                     }
</style>
<body>
<pre>
XX

echo -n '<span class="comment">'

first=1
diffseen=0
lastonly=0

OIFS=$IFS
IFS='
'

# The -r option keeps the backslash from being an escape char.
read -r s

while [[ $? -eq 0 ]]
do
    # Get beginning of line to determine what type
    # of diff line it is.
    t1=${s:0:1}
    t2=${s:0:2}
    t3=${s:0:3}
    t4=${s:0:4}
    t7=${s:0:7}

    # Determine HTML class to use.
    if    [[ "$t7" == 'Only in' ]]; then
        cls='only'
        if [[ $diffseen -eq 0 ]]; then
            diffseen=1
            echo -n '</span>'
        else
            if [[ $lastonly -eq 0 ]]; then
                echo "</div>"
            fi
        fi
        if [[ $lastonly -eq 0 ]]; then
            echo "<div class='diffdiv'>"
        fi
        lastonly=1
    elif [[ "$t4" == 'diff' ]]; then
        cls='diff'
        if [[ $diffseen -eq 0 ]]; then
            diffseen=1
            echo -n '</span>'
        else
            echo "</div>"
        fi
        echo "<div class='diffdiv'>"
        lastonly=0
    elif  [[ "$t3" == '+++'  ]]; then
        cls='plus3'
        lastonly=0
    elif  [[ "$t3" == '---'  ]]; then
        cls='minus3'
        lastonly=0
    elif  [[ "$t2" == '@@'   ]]; then
        cls='at2'
        lastonly=0
    elif  [[ "$t1" == '+'    ]]; then
        cls='plus'
        lastonly=0
    elif  [[ "$t1" == '-'    ]]; then
        cls='minus'
        lastonly=0
    else
        cls=
        lastonly=0
    fi

    # Convert &, <, > to HTML entities.
    s=$(sed -e 's/\&/\&amp;/g' -e 's/</\&lt;/g' -e 's/>/\&gt;/g' <<<"$s")
    if [[ $first -eq 1 ]]; then
        first=0
    else
        echo
    fi

    # Output the line.
    if [[ "$cls" ]]; then
        echo -n '<span class="'${cls}'">'${s}'</span>'
    else
        echo -n ${s}
    fi
    read -r s
done
IFS=$OIFS

if [[ $diffseen -eq 0  &&  $onlyseen -eq 0 ]]; then 
    echo -n '</span>'
else
    echo "</div>"
fi
echo

cat <<XX
</pre>
</body>
</html>
XX

# vim: tabstop=4: shiftwidth=4: noexpandtab:
# kate: tab-width 4; indent-width 4; replace-tabs false;