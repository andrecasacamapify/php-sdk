#!/bin/bash

set -euo pipefail

version=$(sh pipeline/scripts/00-get-next-version.sh $1)

script_dir=$(dirname "$(pwd)/$0")

pushd "$script_dir" > /dev/null

cd ../..

docker build . -f Dockerfile -t "mapify-sdk-test:$version"

popd > /dev/null
