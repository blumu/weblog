@{
  Layout = "page";
  Title = "Publications";
  Tags = "";
  Date = "";
  Description = "Publications";
}
<script type="text/javascript">
function toggle(element) {
if (document.getElementById(element).style.display == "none") {
    document.getElementById(element).style.display = "";
} else {
    document.getElementById(element).style.display = "none";
}
}
</script>

Publications
============

Conference papers and talks
---------------------------

-   **Type homogeneity is not a restriction for safe recursion schemes**, 2009
    *To be published.*
    [Paper (pdf)](safecpda.pdf)

-   **The Safe Lambda Calculus (extended version)**
    With C.-H. Luke Ong.
    Technical report. Long version of the TLCA07 paper.
    [Download paper (pdf)](tlca07-long.pdf)
     | [Download slides (pdf)](tlca07-talk.pdf)

-   **The Safe Lambda Calculus**
    With C.-H. Luke Ong.
    In *Lecture Notes in Computer Science* Vol. 4583 of [Proceedings of
    the 8th International Conference on Typed Lambda Calculi and
    Applications](http://www.springerlink.com/content/95414616686wqj87/?p=2a6f3bd4fc1b45099103c41018da4784&pi=4)
    ([TLCA07](http://www.lsv.ens-cachan.fr/rdp07/tlca.html)),
    pages 39-53. [ © Springer-Verlag](http://www.springer.de/comp/lncs/index.html) Berlin / Heidelberg, 2007.

    [Download (pdf)](safelambda-tlca2007.pdf) |
    [Bibtex](javascript:toggle('tlca.bib'))
    <pre id="tlca.bib" style="display: none;">
    @@INPROCEEDINGS{blumong:safelambdacalculus,
        author = {William Blum and C.-H. Luke Ong},
        title = {The Safe Lambda Calculus},
        booktitle = {TLCA},
        year = {2007},
        pages = {39-53},
        bibsource = {DBLP, http://dblp.uni-trier.de},
        crossref = {DBLP:conf/tlca/2007},
        ee = {http://dx.doi.org/10.1007/978-3-540-73228-0_5}
    }
    </pre>

- **A concrete presentation of Game Semantics**

    Talk given at [](http://www.dur.ac.uk/bctcs.2008/)[Galop
    workshop](http://www.cs.bham.ac.uk/~drg/galop.html) ([ETAPS
    2008](http://etaps08.mit.bme.hu/))

    [Slides (pdf)](bctcs08-slides.pdf)

-  **A concrete presentation of Game Semantics**
    Talk given at [BCTCS 2008](http://www.dur.ac.uk/bctcs.2008/)
    [Slides (pdf)](galop08-slides.pdf)

- **Termination analysis of lambda calculus and a subset of core ML**
    Talk given at [BCTCS 2005](http://www.cs.nott.ac.uk/~gmh/bctcs05.html)

Journal papers
--------------

-   **The Safe Lambda Calculus**
    With C.-H. Luke Ong.
    [Logical Methods in Computer Science (LMCS), Volume 5, Issue 1, Paper 4](http://www.lmcs-online.org/ojs/viewarticle.php?id=424&layout=abstract).
    [Download (pdf)](http://arxiv.org/pdf/0901.2399)

-   **Local computation of beta-reduction--A concrete presentation of Game Semantics (in preparation)**
    With C.-H. Luke Ong.

    [Annals of Pure and Applied Logic (APAL)](http://www.elsevier.com/wps/find/journaldescription.cws_home/505603/description#description),
    (in preparation).
    [Download (pdf)](APAL-localbeta.pdf)

Work, presentations
-------------------

-   **The Safe Lambda Calculus**
 FOCS group lunchtime meeting.
[Slides (pdf)](lunchmeeting.pdf)

-   **The Safe Lambda Calculus**
[BCTCS 2007](http://cms.brookes.ac.uk/bctcs2007/).
[Slides (pdf)](bctcs07-slides.pdf)

-  **DPhil transfer thesis: The Safe Lambda Calculus**
Technical report, submitted August 2006.
[Slides (pdf)](transferThesis.pdf) |
[Bibtex](javascript:toggle('transfer.bib'))

    <pre id="transfer.bib" style="display: none;">
    @@MISC{blum-dphiltransfer,
        author =       {William Blum},
        title =        {The safe lambda calculus},
        howpublished = {DPhil transfer thesis, University of Oxford},
        address =      {http://william.famille-blum.org/},
        year =         {2006},
        month =        {August}
        keywords =     {lambda calculus, game semantics, incrementally justified strategies},
    }
    </pre>

-   **Termination analysis of lambda calculus and a subset of core ML**
Master's Thesis. 2004.
[Download (pdf)](mscthesis.pdf)
| [Sources (Objective Caml)](http://www.famille-blum.org/~william/mscthesis/sct-sources-latest.tar.gz)
| [Bibtex](javascript:toggle('msc.bib'))

    <pre id="msc.bib" style="display: none;">
    @@MASTERSTHESIS{blum-mscthesis,
        AUTHOR = {William Blum},
        TITLE = {Termination analysis of lambda calculus and a subset of core ML},
        SCHOOL = {University of Oxford},
        YEAR = {2004},
        address = {http://william.famille-blum.org/},
        month = {september},
        abstract = {Lee, Jones and Ben-Amram introduced size-change termination,
            a decidable property strictly stronger than termination. They invented 
            a method called the Size-change Principle to analyze it. Based on the 
            work of Jones and Bohr, we propose an extension of the size-change principle 
            to a subset of ML featuring ground type values, higher-order type values and 
            recursively defined functions. This is the first time that the size-change 
            principle is applied to a higher-order functional language. The language 
            handles natively if-then-else and let rec structures. The resulting algorithm 
            produces the expected result for higher-order values but can also analyze 
            the size of ground type values. This enhances the scope of the termination 
            analyzer to some recursively defined function operating on numbers.},
        keywords = {size-change termination, lambda calculus, core ml}
        }
    </pre>


DPhil thesis
------------

-   **The Safe Lambda Calculus**
[D.Phil. thesis, Oxford University Research Archive](http://ora.ouls.ox.ac.uk/objects/uuid:537d45e0-01ac-4645-8aba-ce284ca02673)
[Download (pdf)](http://ora.ouls.ox.ac.uk/objects/uuid%3A537d45e0-01ac-4645-8aba-ce284ca02673/datastreams/THESIS04)
