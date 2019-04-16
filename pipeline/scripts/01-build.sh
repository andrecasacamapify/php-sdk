#!/bin/bash

set -euo pipefail

version=$1
php_version=$2

script_dir=$(dirname "$(pwd)/$0")

pushd "$script_dir" > /dev/null

cd ../..

echo "Building PHP$php_version image for version $version"
sed "s/VERSION/$php_version/g" $script_dir/../templates/Dockerfile > $script_dir/Dockerfile$php_version
docker build . -f $script_dir/Dockerfile$php_version -t "mapify-sdk-test:$php_version.$version" 

popd > /dev/null
