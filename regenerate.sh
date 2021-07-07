#!/usr/bin/env bash

set -eu
set -o pipefail

dotnet tool restore
echo 'regenerate' | dotnet fsi tools/update.fsx