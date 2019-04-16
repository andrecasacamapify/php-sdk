#!/bin/bash

set -euo pipefail

script_dir=$(dirname "$(pwd)/$0")

username=$1
api_key=$2
url=$3
new_version=$4

pushd "$script_dir" > /dev/null

cd ../..

echo "Bumping to $new_version...\n\n"

git config --global user.email "jenkins@mapify.ai"
git config --global user.name "Jenkins"

git tag -a $new_version -m "Version $new_version"
git push origin "$new_version"

echo "Tag $new_version pushed\n\n"

curl -XPOST -H'content-type:application/json' \
    "https://packagist.org/api/update-package?username=$username&apiToken=$api_key" \
    -d "{\"repository\":{\"url\":\"$url\"}}"

must_wait=1
tried_times=0
while [[ $must_wait -eq 1 && $try_times -le 20 ]]; do
    set +e
    echo "$(date) trying to get the package..." 
    try_times+=1
    docker run --rm --interactive composer require "mapify/sdk:$new_version"
    must_wait=$?
    set -e
    echo "$(date) Waiting for package to be ready" 
    sleep 10s
done

popd > /dev/null
