#!/bin/bash
export CAROOT=$(pwd)
echo "Set CAROOT folder: " $CAROOT
echo "Run: mkcert install" 

./mkcert -install

