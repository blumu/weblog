Research
========

 - layout: page
 - title: Research
 - description: Research

Check out the [publications pages](/research/publications/) to learn more
about my research in formal computer science. Tools developed in
relation with this research can be [found here](/research/tools/).

------------------------------------------------------------------------

Security and Reinforcement Learning
-----------------------------------

CyberBattleSim is an experimentation research platform to investigate the interaction of automated agents operating in a simulated abstract enterprise network environment. The simulation provides a high-level abstraction of computer networks and cyber security concepts. Its Python-based Open AI Gym interface allows for training of automated agents using reinforcement learning algorithms. The simulation environment is parameterized by a fixed network topology and a set of vulnerabilities that agents can utilize to move laterally in the network.…

- Microsoft Research blog at https://www.microsoft.com/security/blog/2021/04/08/gamifying-machine-learning-for-stronger-security-and-ai-models/
- https://github.com/microsoft/CyberBattleSim

_With Christian Seifert, Michael Betser, William Blum, James Bono, Kate Farris, Emily Goren, Justin Grana, Kristian Holsheimer, Brandon Marken, Joshua Neil, Nicole Nichols, Jugal Parikh, Haoran Wei._


Neural-based Fuzzing
--------------------

Fuzzing is a popular dynamic program analysis technique used to find vulnerabilities in complex software. Fuzzing involves presenting a target program with crafted malicious input designed to cause crashes, buffer overflows, memory errors, and exceptions. Crafting malicious inputs in an efficient manner is a difficult open problem and often the best approach to generating such inputs is through applying uniform random mutations to pre-existing valid inputs (seed files). We present a learning technique that uses neural networks to learn patterns in the input files from past fuzzing explorations to guide future fuzzing explorations. In particular, the neural models learn a function to predict good (and bad) locations in input files to perform fuzzing mutations based on the past mutations and corresponding code coverage information. We implement several neural models including LSTMs and sequence-to-sequence models that can encode variable length input files. We incorporate our models in the state-of-the-art AFL (American Fuzzy Lop) fuzzer and show significant improvements in terms of code coverage, unique code paths, and crashes for various input formats including ELF, PNG, PDF, and XML.

- https://www.microsoft.com/en-us/research/publication/not-all-bytes-are-equal-neural-byte-sieve-for-fuzzing/
- https://www.microsoft.com/en-us/research/blog/neural-fuzzing/

_With Mohit Rajpal and  Rishabh Singh._

Lambda calculus evaluation
--------------------------

A method to evaluate untyped lambda terms based on a term tree-traversing technique inspired by Game Semantics and judicious use of eta-expansion. As traversals explore nodes of the term tree, they dynamically eta-expand some of the subterms in order to locate their non-immediate arguments. A quantity called dynamic arity determines the necessary amount of eta-expansion to perform at a given point. Traversals are finitely enumerable and characterize the paths in the tree representation of the beta-normal form when it exists. Correctness of the evaluation method follows from the fact that traversals implement leftmost linear reduction, a non-standard reduction strategy based on head linear reduction of Danos-Regnier.

- https://www.microsoft.com/en-us/research/publication/evaluating-lambda-terms-with-traversals-2/
- https://www.sciencedirect.com/science/article/abs/pii/S0304397519305316?via%3Dihub
- Rust and F# implementations: https://github.com/blumu/travnorm

The resumable monad
-------------------

We define a new monad and its associated F# syntactic sugar resumable { ... } to express computations that can be interrupted at specified control points and resumed in subsequent executions while carrying along state from the previous execution.

Resumable expressions make it simpler to write idempotent and resumable code which is often necessary when writing cloud services code.

- http://blumu.github.io/ResumableMonad/
- https://github.com/blumu/ResumableMonad/
Game semantics
--------------

In my [D.Phil thesis (2009)](http://ora.ouls.ox.ac.uk/objects/uuid:537d45e0-01ac-4645-8aba-ce284ca02673) I introduced a novel presentation of Game
Semantics based on a generalization of the theory of traversals.
Traversals were originally introduced by Ong in the context of higher order recursion schemes.
In my thesis, I extend the notion of traversals to the simply-typed lambda calculus and other extensions of it (e.g., PCF).
I then introduce an alternative presentation of game semantics, called the revealed game semantics, where internal moves are preserved.
Finally I formalize a correspondence between the game semantic denotation
of a lambda term and its set of traversals.

> **The Correspondence Theorem**
> $ \newcommand{\theroot}{\circledast} % the root of the > computation tree
>  \newcommand{\lsem}{[\![} % \llbracket
>   \newcommand{\rsem}{]\!]} % \rrbracket
>   \newcommand{\sem}[1]{{[\![#1]\!]}}
>   \newcommand\travset{\mathcal{T}rav}
>   \newcommand{\filter}{\upharpoonright}
>   \newcommand{\syntrevsem}[1]{{\langle\!\langle #1 > \rangle\!\rangle}_{\sf s}}$
> 
> For every simply-typed term $\Gamma \vdash M :T$, we can > construct a bijection $\varphi_M$ from the set of traversals > over some abstract syntax representation
> of $M$ and the revealed game semantic denotation of $M$. > Further, we can construct a bijection $\psi_M$ from the set > of traversals projected
> at the root $\theroot$ of the abstract syntax tree of $M$ > and the standard game semantic denotation of $M$:
> $$\begin{eqnarray*}
>  \varphi_M  &:& \travset(M : T)^\star \stackrel{\cong}> {\longrightarrow} \syntrevsem{M} \\
>  \psi_M  &:& \travset(M : T)\filter \theroot \stackrel{\cong}> {\longrightarrow} \sem{M} \enspace .
> \end{eqnarray*}
> $$

The full technical details and proof this theorem can be found in Chapter 4 of my DPhil thesis.
I have also extracted the relevant part in a [technical report](APAL-localbeta.pdf).
I also gave an [overview of the correspondence result and its proof](galop08-slides.pdf) at the Galop 2008 workshop.

Based on this result, I implemented a pedagogic tool for teaching Game semantics and the theory of traversals.
The tool, written in F# and OCaml, can be downloaded [here](tools/).


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
computations called the safety restriction.
For the nitty gritty details I refer you to the [online publication
of my thesis](https://william.famille-blum.org/research/publications/).

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

