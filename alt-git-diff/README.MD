Obsolete Git Diff
===

This works as well but the side by side comparison takes up >100% of the width if text file that's compared has no line breaks between long lines. If you can figure out how to fix that using css, then this version is much better because they're side by side comparisons with line numbers.

Suggestion:  Maybe use a php library that adds line breaks at a certain width. You would add this code after `$gitd = $repo->run(sprintf("diff %s^ %s", $_GET["current_hash"], $_GET["current_hash"]));`.