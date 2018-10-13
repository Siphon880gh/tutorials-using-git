Tutorials Using Git Branches and Commits
===
By Weng Fei Fung. There are many different frontends, bundlers, etc that you may need refreshers on and different websites and videos that teach too differently and some, teach ineffectively. We can take advantage of git to author uniform lessons that show code changes along the way of setting up webpack, react, etc, for example. This tool generates a clickable Tree of the branches and commits. 

As the learner clicks through commits on the tree, they go through the steps of setting up an app and they see where and what the code changes are, because there will be a webpage showing the git diff between that commit they clicked and the earlier commit along the tree. 

As they hover their mouse over a commit, they see your tutorial for that step of the app (Markdown supported). So they not only see where the code changes are, but they're instructed on it. The learner sees a timeline of all code changes and an explanation why.

The tutorial can be multifaceted. The tree can contain branches. Say you want to deviate from the usual setup of webpack at some point, then the learner can see where they could and what other features become prohibitive once taking that route. You can combine multiple tutorials using branches.

Restrictions
--
- No restrictions on programming languages! You can even make snapshot tutorials of Android and iOS apps. Just make sure to add the appropriate patterns to .gitignore so your tutorial does not get bogged down.
- Not only for beginners. You can make tutorials to refresh yourself on any tech that you use infrequently, so you don't have to start from scratch everytime.


Authoring
---
- Tutorials or explanations are added through `git notes`.
- Be careful about rebasing (even as simple as rewording) or amending commits because that'd change the hash number. Then the notes will no longer be associated with that commit or any commits downstream. I suggest once you are sure the commits are final, then you start adding notes to the appropriate commits. This means you are adding notes to past commits.
- You can add notes to past commits with either:  
`git notes edit 0ea7d1e05a8629ae72a98955423f5094561d59ac`  
`git notes add 0ea7d1e05a8629ae72a98955423f5094561d59ac -m "hi"`
`
- You can add notes to the current commit with either:  
`git notes edit`  
`git notes add -m "hi"`
- Notes can be in Markdown. The start.php would format Markdown text.
- Using `edit` lets you add notes using a text editor so it's easier to enter lines of notes.

Installation
---
- Copy start.php and /deps to the repos of interest.
- Run start.php on a server:
    - It will show the tree of repository commits.
    - If you haven't commited anything, there'll be a blank tree.

Usage
--
1. The idea is a new platform for creating programming tutorials by taking advantage of git diff to see code changes from step to step and git notes that explain those steps. This tool will let you see git diff and notes in one place. Git notes is versatile because you can enter multiple lines of explanations and push them to the github repos (though by default, pushing does not include notes). Your git notes can have markdown and this page will effortlessly format the various headings, lists, emphasis, etc.
2. The commits that are actually steps of the code changes start from the bottom. You see notes by hovering the mouse over. You see the code changes of the previous commit and current commit by clicking the commit. Open all from bottom.  
2b. Hold shift while clicking a commit for the alternate git diff view that's side by side (draw back is there's no word wrapping. Maybe in a future version this would be the newer git diff with word wrapping).
3. How to port to other git repos: Download their repo. Then place start.php and /deps there. Run start.php on a server and you will see the git commits step by step.