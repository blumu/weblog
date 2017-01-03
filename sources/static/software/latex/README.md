The pstring LaTeX package
=========================

This package lets you typeset justiﬁed sequences, also called pointing strings.
It's used for instance, in research papers about Game Semantics to represent sequence of game moves
with their associated justification pointers.

See [http://william.famille-blum.org/software/latex/]() for latest information about this package.

Author
------

William Blum (william.blum@gmail.com)

Platform
--------

Tested with MikTeX on Windows. It was reported to work on other platform as well but I have not tested it myself.

Package dependencies
--------------------

The following packages are required when compiling with `latex` to postscript:

    pstricks
    pst-node

If compiling to PDF using `pdflatex` then the `PGF`/`tikz` packages are used instead to render
the pointers:

    pgfcore

License
-------

This material is released in the Public domain.