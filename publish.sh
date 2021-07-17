#!/usr/bin/env bash

set -o pipefail

wwwroot=../output
azure_webgit=https://luweiblog.scm.azurewebsites.net:443/luweiblog.git

PURGE='false'
NO_CONFIRM='false'

red=`tput setaf 1`
green=`tput setaf 2`
reset=`tput sgr0`

if [[ "$1" == "purge" ]];
then
    PURGE='yes'
    shift
fi

if [[ "$1" == "--y" ]];
then
    echo "No user confirmation (--y)"
    NO_CONFIRM='yes'
fi

if [[ $PURGE == 'true' ]];
then
    echo "Purging $wwwroot"
    rm -Rf $wwwroot
fi

set -eu


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

if [[ $NO_CONFIRM == 'true' ]];
then
    read -p "Press enter to continue"
fi

if [[ -z "${GIT_PASSWORD-}" ]]; then
    echo "${red}Set the GIT_PASSWORD variable and run the script again to publish!" >&2
    exit 3
fi


pushd $wwwroot
git add .
git commit -a -m "Updating site ($(date))" || echo "Nothing commited"
git push
popd

echo "${green}Successfully published${reset}"