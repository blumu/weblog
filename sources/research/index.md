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

In my [D.Phil thesis (2009)](http://ora.ouls.ox.ac.uk/objects/uuid:537d45e0-01ac-4645-8aba-ce284ca02673) I introduced a novel presentation of Game
Semantics based on a generalization of the theory of traversals.
Traversals were originally introduced by Ong in the context of higher order recursion schemes.
In my thesis, I extend the notion of traversals to the simply-typed lambda calculus and other extensions of it (e.g., PCF).
I then introduce an alternative presentation of game semantics, called the revealed game semantics, where internal moves are preserved.
Finally I formalize a correspondence between the game semantic denotation
of a lambda term and its set of traversals.

**The Correspondence Theorem**
$ \newcommand{\theroot}{\circledast} % the root of the computation tree
 \newcommand{\lsem}{[\![} % \llbracket
  \newcommand{\rsem}{]\!]} % \rrbracket
  \newcommand{\sem}[1]{{[\![#1]\!]}}
  \newcommand\travset{\mathcal{T}rav}
  \newcommand{\filter}{\upharpoonright}
  \newcommand{\syntrevsem}[1]{{\langle\!\langle #1 \rangle\!\rangle}_{\sf s}}$

For every simply-typed term $\Gamma \vdash M :T$, we can construct a bijection $\varphi_M$ from the set of traversals over some abstract syntax representation
of $M$ and the revealed game semantic denotation of $M$. Further, we can construct a bijection $\psi_M$ from the set of traversals projected
at the root $\theroot$ of the abstract syntax tree of $M$ and the standard game semantic denotation of $M$:
$$\begin{eqnarray*}
 \varphi_M  &:& \travset(M : T)^\star \stackrel{\cong}{\longrightarrow} \syntrevsem{M} \\
 \psi_M  &:& \travset(M : T)\filter \theroot \stackrel{\cong}{\longrightarrow} \sem{M} \enspace .
\end{eqnarray*}
$$

The full technical details and proof this theorem can be found in Chapter 4 of my DPhil thesis.
I have also extracted the relevant part in a [technical report](APAL-localbeta.pdf).
I also gave an [overview of the correspondence result and its proof](galop08-slides.pdf) at the Galop 2008 workshop.

Based on this result, I implemented a pedagogic tool for teaching Game semantics and the theory of traversals.
The tool, written in F# and OCaml, can be downloaded [here](tools.html).


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

The major part of my DPhil thesis concerns the study of a syntactic constraint for higher-order
computations called the safety restiction.
For the nitty gritty details I refer you to the [online publication
of my thesis](http://william.famille-blum.org/research/publications.html).

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

