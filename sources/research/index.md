@{
  Layout = "page";
  Title = "Research";
  Tags = "";
  Date = "";
  Description = "Research";
}
Research
========

Check out the [publications pages](publications.html) to learn more
about my research in formal computer science. Tools developed in
relation with this research can be [found here](tools.html).  

------------------------------------------------------------------------

Game semantics
--------------

One of the contribution of my DPhil is a novel presentation of Game
Semantics based on a generalization of the theory of traversals
introduced by Ong. The [unpublished technical report 
(pdf)](APAL-localbeta.pdf) contains the full technical details. The
concepts are summarized in [the deck (pdf)](galop08-slides.pdf) of the
talk I gave at Galop 2008.

Higher-order recursion schemes
------------------------------

The study of the safety restriction led me to the study of higher-order
recursion schemes. In an unpublished paper I showed that the type
homogeneity constraint included as part of the original definition by
Knapik et al. is in fact not necessary. The 12-page [proof
(pdf)](safecpda.pdf) consists in a game semantic argument based on the
theory of traversals which is presented in details in my DPhil thesis.

The safe lambda calculus
------------------------

My DPhil concerns the study of a syntactic constraint for higher-order
computations called the safety restiction. I refer you to the thesis on
the [publication
page](http://william.famille-blum.org/research/publications.html) for
the nitty gritty details.

Termination analysis of higher-order programs
---------------------------------------------

Based on the work of Jones and Bohr, I propose an extension of the
size-change principle introduced by Lee, Jones and Ben-Amram to a subset
of ML featuring ground type values, higher-order type values and
recursively defined functions. This constitutes the first attempt to
adapt the size-change principle to a higher-order functional language
ala ML featuring `if-then-else`branching and
`let rec` definitions. I have implemented a
tool based on the this work which is able to prove termination of
non-trivial higher-order programs of both ground and higher-order types.
This gives a termination decision tool for some subset of recursively
defined function on natural numbers.

 This is research was pursued as part of my Master in Computer Science
(2004).

