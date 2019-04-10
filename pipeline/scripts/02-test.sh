#!/bin/bash

set -euo pipefail

valid_api_key=$1
public_key=$2
base_uri=$3
version=$(sh pipeline/scripts/00-get-next-version.sh $4)

script_dir=$(dirname "$(pwd)/$0")

pushd "$script_dir" > /dev/null

cd ../..

docker run -e TEST_VALID_API_KEY="$valid_api_key" -e TEST_PUBLIC_KEY_BASE64="$public_key" -e TEST_BASE_URI="$base_uri" -v $(pwd)/tests/results:/sdk/tests/results "mapify-sdk-test:$version" -c php composer.phar run test

popd > /dev/null
