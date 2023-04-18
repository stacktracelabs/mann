#!/usr/bin/env bash

set -u -e

if [ -z "$(git status --porcelain)" ]; then
  VERSION=$(npm version $1)
  npm run build
  git push && git push --tags
  npm pack && npm publish
  rm -f *.tgz

  echo "🚀 Mann $VERSION released!"
else
  echo "Unable to release: There are uncommitted changes."
fi

