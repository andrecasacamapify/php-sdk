#!/bin/bash

set -euo pipefail

script_dir=$(dirname "$(pwd)/$0")

pushd "$script_dir" > /dev/null

cd ../..

git clean -xfd

wget http://stedolan.github.io/jq/download/linux64/jq -O $script_dir/jq

chmod 700 pipeline/scripts/jq

popd > /dev/null