#!/bin/bash

set -euo pipefail

script_dir=$(dirname "$(pwd)/$0")

username=$1
api_key=$2
url=$3
#Punping version to next version
new_version=$(sh pipeline/scripts/00-get-next-version.sh $4)

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

docker run --rm --interactive --tty composer require "mapify/sdk:$new_version"

popd > /dev/null
