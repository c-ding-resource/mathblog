cd C:\xampp\htdocs\wordpress\wp-content\plugins\common-functions\library\NewMDE\test
pandoc  -F pandoc-crossref -M autoEqnLabels=true -F pandoc-citeproc  equations.tex -s -o equations.md --mathjax
