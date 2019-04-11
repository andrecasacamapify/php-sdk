#!/bin/bash

set -euo pipefail

public_key=$1
base_uri=$2
version=$(sh pipeline/scripts/00-get-next-version.sh $3)
key_file=$4

script_dir=$(dirname "$(pwd)/$0")

pushd "$script_dir" > /dev/null

cd ../..

wget https://github.com/stedolan/jq/releases/download/jq-1.6/jq-linux64 -O $script_dir/jq
$jq=$script_dir/jq

gcloud auth activate-service-account --key-file="$key_file"
google_token=$( gcloud auth print-access-token )

authorization_token=$(curl -X POST \
  $base_uri/login \
  -H 'Content-Type: application/json' \
  -d "{
	\"token\": \"$google_token\",
	\"type\": \"accessToken\",
	\"provider\": \"google\"
}" | $jq .authorizationToken --raw-output)

api=$(curl -X POST \
  $base_uri/api \
  -H "Authorization: Bearer $authorization_token" \
  -H 'Content-Type: application/json' \
  -d '{
	"name": "api test sdk",
	"claims": [{
		"name": "claim name",
		"description": "claim description"
	}]
}' | $jq . --raw-output)

apikey=$(curl -X POST \
  $base_uri/apikey \
  -H "Authorization: Bearer $authorization_token" \
  -H 'Content-Type: application/json' \
  -d "{
	\"name\": \"credential test sdk\",
	\"apis\": [$api]
}" | $jq . --raw-output)
valid_api_key=$(echo $apikey | $jq .key --raw-output)

docker run -e TEST_VALID_API_KEY="$valid_api_key" -e TEST_PUBLIC_KEY_BASE64="$public_key" -e TEST_BASE_URI="$base_uri" -v $(pwd)/tests/results:/sdk/tests/results "mapify-sdk-test:$version" -c php composer.phar run test

curl -X DELETE \
  $base_uri/apikey/$valid_api_key \
  -H "Authorization: Bearer $authorization_token"

api_key=$(echo $api | $jq .key --raw-output)
curl -X DELETE \
  $base_uri/api/$api_key \
  -H "Authorization: Bearer $authorization_token"

popd > /dev/null
