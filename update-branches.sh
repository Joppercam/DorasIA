#!/bin/bash

# Update develop branch with feature/triple-import-data changes
git checkout develop
git pull
git merge --no-ff feature/triple-import-data -m \Merge feature/triple-import-data: Enhanced data import with triple capacity\n
# Update main branch
git checkout main
git pull
git merge --no-ff develop -m \Merge develop with enhanced data import functionality\n
# Return to feature branch
git checkout feature/triple-import-data

echo \All branches updated successfully!\n
