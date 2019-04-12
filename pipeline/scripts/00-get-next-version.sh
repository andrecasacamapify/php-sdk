#!/bin/bash

EXISTING_VERSION_TAG=$(git describe --exact-match --tags HEAD || echo "false")
if [ "$EXISTING_VERSION_TAG" != false ]; then
    echo $EXISTING_VERSION_TAG
    exit
fi

# get highest tag number
LAST_VERSION_TAG=$(git describe --abbrev=0 --tags || echo "0.0.0")
LAST_VERSION=${LAST_VERSION_TAG:-1}

# get number parts and increase last one by 1
LAST_VERSION_MAJOR=$(echo $LAST_VERSION_TAG | cut -d'.' -f1 | sed -e "s/v//g")
LAST_VERSION_MINOR=$(echo $LAST_VERSION_TAG | cut -d'.' -f2)
LAST_VERSION_PATCH=$(echo $LAST_VERSION_TAG | cut -d'.' -f3)

# get major and minor version numbers
PACKAGE_VERSION=$1
PACKAGE_VERSION=${PACKAGE_VERSION:-1}

PACKAGE_VERSION_MAJOR=$(echo $PACKAGE_VERSION | cut -d'.' -f1)
PACKAGE_VERSION_MINOR=$(echo $PACKAGE_VERSION | cut -d'.' -f2)

# determine new version
NEW_VERSION_MAJOR=$LAST_VERSION_MAJOR
NEW_VERSION_MINOR=$LAST_VERSION_MINOR
NEW_VERSION_PATCH=0

SKIP_TAGGING=false

if [ -z "$LAST_VERSION_TAG" ]; then # use package version numbers if no previous tags exist
    NEW_VERSION_MAJOR=$PACKAGE_VERSION_MAJOR
    NEW_VERSION_MINOR=$PACKAGE_VERSION_MINOR

elif [ "$PACKAGE_VERSION_MAJOR" -lt "$LAST_VERSION_MAJOR" ]; then
    SKIP_TAGGING=true # can't decrement version - skip tagging

elif [ "$PACKAGE_VERSION_MAJOR" -gt "$LAST_VERSION_MAJOR" ]; then
    NEW_VERSION_MAJOR=$PACKAGE_VERSION_MAJOR
    NEW_VERSION_MINOR=$PACKAGE_VERSION_MINOR

elif [ "$PACKAGE_VERSION_MINOR" -lt "$LAST_VERSION_MINOR" ]; then
    SKIP_TAGGING=true # can't decrement version - skip tagging

elif [ "$PACKAGE_VERSION_MINOR" -gt "$LAST_VERSION_MINOR" ]; then
    NEW_VERSION_MINOR=$PACKAGE_VERSION_MINOR

else
    NEW_VERSION_PATCH=$((LAST_VERSION_PATCH+1))
fi

# skip tagging if a versioning mismatch has occurred
if [ "$SKIP_TAGGING" = true ]; then
    echo "*ERROR* Skipping tagging due to versioning mismatch"
    exit 1
fi

# create new tag
echo "$NEW_VERSION_MAJOR.$NEW_VERSION_MINOR.$NEW_VERSION_PATCH"