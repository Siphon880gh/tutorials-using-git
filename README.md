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
- You can add notes to past commits with:  
`git notes add 0ea7d1e05a8629ae72a98955423f5094561d59ac -m "hi"
`
- You can add notes to the current commit with either:  
`git notes edit` or  
`git notes add -m "hi"`
-Notes can be in Markdown. The start.php will show the formatting, making your tutorial intuitive.

Installation
---
- Make sure you have shell access. MAMP is a good tool. Without shell access, this version of git diff won't work (the future git diff web version that you can preview by clicking a commit while holding Shift would not have this limitation).
- Copy these files into the folder that's a repository. If it is not yet a repository, make sure to `git init`. Copy all folders and files except .git.
- Just run start.php in a server, and you're all good to go. 
    - It will show the tree of the repository. If you run this and you haven't setup a git repos, you'll see this error:  
`Fatal error: Uncaught Exception: "/Users/..." is not a git repository`
    - If you haven't commited anything, there'll be no tree when you run start.php, of course.

Easy instructions for the learner
--
- Here's a tree of all the major steps for the app/boilerplate/bundler/etc.
- Start from the bottom of the tree and click the topic. Each topic is cumulative and dependent on the previous topic.
- You'll see what new codes there are from the previous step. Try to mimic these steps.
- And move your mouse over the step to see the tutorial for that step. If it says there are No Notes, then the step is likely self explanatory and the author did not add any instructions.


Advanced instructions for the learner
---
- Move mouse over the commits for any notes that were added with `git notes edit`, ideally where the author write parts of the tutorial in Markdown format.
- Click a commit for a git diff with the commit more upstream, showing what and where the changes of the code are and ideally that the tutorial is explaining at that commit of the tree.
- Hold shift while clicking a commit for the alternate git diff view that's side by side (draw back is there's no word wrapping. Maybe in a future version this would be the newer git diff with word wrapping).