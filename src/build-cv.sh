#!/bin/zsh
# Renders src/cv.html to DerrickBrown.pdf (the "Download CV" file) with headless Chrome.
cd "${0:A:h}/.."
"/Applications/Google Chrome.app/Contents/MacOS/Google Chrome" --headless=new --disable-gpu --no-pdf-header-footer \
  --print-to-pdf="$PWD/DerrickBrown.pdf" "file://$PWD/src/cv.html" 2>/dev/null
ls -l DerrickBrown.pdf
