Tutorials Using Git Branches and Commits
===
By Weng Fei Fung  
*What is:* This is a walkthrough tutorial generator and reader for git repos by leveraging the power of git diff and git notes. You can get your team up to speed or personally review how to setup different boilerplates (like webpack). This tool show a list of branches and commits in order of creation from the bottom. You may add notes to particular commits with the command git note. Those notes can have multiple lines and Markdown styles so that the walkthroughs look like formatted documents.

*About git notes:* Git does not by default fetch and push notes. Refer to section on Author for more information.

*How to install:* Place start.php and /deps in any git repos. Run start.php on a PHP server.

*How to use:* Commits with the note icon have notes you can view by hovering the mouse cursor over. Click commits in sequential order to step through the code changes and view any notes. Hold shift while clicking a commit to view the alternate git diff that is side by side.

*Suggested use of branches:* You can use branches to show how the code could change at different points, for example, with different setups of webpack.

No Restrictions
--
- No restrictions on programming languages! You can even make walkthrough tutorials of Android and iOS apps. Use .gitignore so your commit git diffs are not bogged down.


Author
---
- Git notes edit and add:
    - Add notes to the current commit  (multilines vs single line, respectively):
        - `git notes edit`  
        - `git notes add -m "your_note_here"`
    - Add notes to past commits: 
        - `git notes edit COMMIT_ID`  
        - `git notes add COMMIT_ID -m "your_note_here"`

- Online repos:
    - Notes are not fetched or pushed by default.
    - **Fetching**  
        - To fetch by *default*:  
            1. You can create a .gitconfig file with these settings to fetch notes by default:  
                ```
                [remote "origin"]  
                url = your_git_reps_url  
                fetch = +refs/heads/*:refs/remotes/origin/*  
                fetch = +refs/notes/*:refs/notes/*  
                ```
            2. Then for every user, they need to run this command to apply .gitconfig:  
                `git config --local include.path ../.gitconfig`
        - To fetch *manually*:  
            `git fetch origin refs/notes/commits:refs/notes/commits`  
            `git fetch origin "refs/notes/*:refs/notes/*"`
        - Make sure to fetch after cloning or pulling.
    - **Pushing**
        - You have to push manually:  
            `git push origin refs/notes/commits`  
            `git push origin "refs/notes/*"`

- Markdown: You can add Markdown styles to have the tool show formatted documents.

- Caveat: Renaming or rebasing commits will change the commit's ID. It also changes the ID's of downstream commits. This will cause the note to be lost. You'll want to revisit the dangling commits:
    1. `git fsck --lost-found`
    2. `git notes show COMMIT_ID`
    3. Once you find the right notes, copy and paste it over.

Learner
---
- Simply drop start.php and /deps into the git repos. Then you can view all commits of that project in start.php.
- Click a commit to view the code changes at that point. I recommend viewing from the first commits at the bottom of the list, then work your way up.
- If you see a note icon, hover the mouse over. The author may have provided a formatted document explaining some code changes.
