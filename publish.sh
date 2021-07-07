#!/usr/bin/env bash

#set -eu
set -o pipefail

wwwroot=../output
azure_webgit=https://luweiblog.scm.azurewebsites.net:443/luweiblog.git

if [[ "$1" == "purge" ]];
then
    echo "Purging $wwwroot"
    rm -Rf $wwwroot
fi

if [[ "$CODESPACES" == "true" ]];
then
    echo 'Running under GitHub codespaces, disabling default credential helper'
    export GIT_ASKPASS=/workspaces/weblog/git-askpass-helper.sh
    export GIT_CONFIG_COUNT=1
    export GIT_CONFIG_KEY_0=credential.helper
    export GIT_CONFIG_VALUE_0=
fi

if [[ -d "$wwwroot/.git" ]];
then
    echo "Azure Website git repo detected at $wwwroot"
else 
    echo "Cloning Azure Website LocalGit to $wwwroot (Expecting password in \$GIT_PASSWORD)"
    git clone --filter=tree:0 https://\$luweiblog@luweiblog.scm.azurewebsites.net:443/luweiblog.git $wwwroot
fi

echo 'Regnerating the website under $wwwroot'
./regenerate.sh

echo "Publishing website via Azure website local Git repo ($azure_webgit)"
read -p "Press enter to continue"

pushd $wwwroot
git add .
git commit -a -m "Updating site ($(date))" 
git push
popd