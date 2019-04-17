#!/bin/bash

set -euo pipefail

public_key=$1
base_uri=$2
version=$3
valid_api_key=$4
php_version=$5

script_dir=$(dirname "$(pwd)/$0")
pushd "$script_dir" > /dev/null

cd ../..

echo "Testing PHP$php_version"
docker run -e TEST_VALID_API_KEY="$valid_api_key" -e TEST_PUBLIC_KEY_BASE64="$public_key" -e TEST_BASE_URI="$base_uri" "mapify-sdk-test:$php_version.$version" -c php composer.phar run test

popd > /dev/null
