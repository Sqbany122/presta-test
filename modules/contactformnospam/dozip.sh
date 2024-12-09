#!/bin/bash

module=$(basename $PWD)
module=${module,,}
if [ -r "$module" ]; then
  echo "The directory $module already exists"
  exit 1
else
  mkdir $module
fi
mkdir -p zip

version=`sed -n -e '/\$this->version/s/.*= .\(.*\).;$/\1/p' $module.php |
  sed 's/[^ ]* //'`
echo "Version: $version"

files='
override/controllers/front/ContactController.php
override/controllers/front/index.php
override/controllers/index.php
override/index.php
nospam.js
logo.png
dozip.sh
contactformnospam.php
index.php
'
tar -cf - $files | tar -C $module -xf - && \
rm -f zip/${module}_$version.zip && \
zip -r zip/${module}_$version.zip $module && \
rm -rf $module
