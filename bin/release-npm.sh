#!/usr/bin/env bash

set -u -e

if [ -z "$(git status --porcelain)" ]; then
  npm run build
  VERSION=$(npm version $1)
  git push && git push --tags
  npm pack && npm publish
  rm -f *.tgz

  echo "🚀 Mann $VERSION released!"
else
  echo "Unable to release: There are uncommitted changes."
fi

