# LaTeXDaemon changelog

## 23 Nov 2010 - Version 0.10 Build 46

-   New: The clean-up commands now deletes auxiliary files from the tex file directory as well as the TexAux directory.
-   New: Project built with Visual Studio 2010
-   Fix: Due to a bug in MikTeX, the option 'preamble=no' caused TeX to fail with the error 'Not enough room in an internal buffer'.
-   Fix: Bibtex command would fail if 'TexAux' option is used and the main .tex includes other .tex files from subdirectories.

## 11 May 2009 - Version 0.10 Build 45

-   Dependencies were not properly updated after the first compilation.
-   Fix typo in prompt.

## 2 November 2008 - Version 0.10 Build 44

-   Bug fixed:automatic file change detection now works with network path of the form '\\\\MACHINE\\folder\\file.tex'

## 23 June 2008 - Version 0.10 Build 43

-   New: It is now possible to create a local configuration file (with the .daemon extension) that is automatically read by the daemon upon loading of the corresponding .tex file. Each line of the file must contain a valid LatexDaemon command. Comments are prefixed with two slash symbols: //.
-   New: It is now possible to add commands in the .tex file to be executed upon loading by latexdemon. Each command must be prefixed with '%Daemon&gt;' and must occur before any latex command. For instance:

        %Daemon> ini=latex
        %Daemon> afterjob=dvipspdf
        %Daemon> filter=err+warn
        %Daemon> custom_args=-synctex=-1
        \documentclass{article}
        ...

-   New: custom parameters can be passed to latex using the variable 'custom\_args'
-   New: custom parameters can be specified to the ps2pdf command
-   New 'cleanup' command that deletes files from the auxiliary files directory
-   Bug fixed: holding the scrollbar in the command prompt window (in order to pause the scrolling) for too long could cause a deadlock.
-   Bug fixed: the file passed in parameter was ignored if the option -filter-... was used
-   Bug fixed in digest computation (when specified length is smaller than the file size)
-   Bug fixed: The dependency did not work properly in internal preamble mode
-   Processes started from the main thread can now also be killed with CTRL+C (before this would kill the entire daemon process).
-   Few other bugs fixed

## 18 June 2008 - Version 0.10 Build 42

-   New: It is not required anymore to move the preamble to a separate file. The preamble can now be extracted automatically from the main .tex file. (This features uses TeX code from the 'mylatex' package.)
-   New: a compilation can now be interrupted with CTRL+BREAK without killing latexdaemon.
-   Bug fixed: whenever a non-preamble tex source file is modified, if the preamble is currently being recompiled then the compilation is no longer interrupted.
-   Change: accepted values for the --preamble switch are now 'yes' or 'no'.
-   Change: stop reporting file modificatations for subdirectories.

## 8 June 2008 - Version 0.9 Build 40

-   Change: The switch --src-special is now deactivated when using pdflatex instead of latex.
-   Change: Call to the CreateThread Win32 API are now replaced by the CRT API \_beginthreaex.
-   Change: If a .out file is generated by the hyperref package then it is backed-up before compilation and restored after an aborted compilation. This prevents the file from becoming corrupted if the compilation is aborted due to a change in the source .tex file.
-   Bug fixed: problem if the main .tex file name contains spaces

## 13 May 2008 - Version 0.9 Build 39

-   Change: a different methdo to hook TeX file inclusion is used which is compatible with the pdfsync package. With the previous method the included file were not properly recorded in the generated .pdfsync file.
-   New command 'mi' to build the index file using makeindex.
-   New: auxiliary files created during the latex compilation are stored in a subdirectory. By default this subdirectory is named TexAux. This name can be changed using the command line option -aux-directory=DIR. If DIR is set to the empty string then the feature is deactivated i.e. the auxiliary files are stored along with the .tex file. If a .pdfsync file is created during the compilation process, then it is moved back to the folder containing the .pdf file.
-   Change of behaviour: before, the new dependencies were not reloaded when the latex compilation fails. This was to prevent from reloading an empty list of dependencies created when compiling a latex document in a \`\`corrupted'' state. Now if the latex compilation returns an error code, the detected dependencies are added to the previous ones instead of replacing them. The dependencies are replaced with the newly detected ones only if the latex compilation succeeds.
-   New command dvipspdf: it runs dvips and then ps2pdf. It can also be set as a value for afterjob.
-   The name of the preamble format file now contains the name of the ini file. This allows the user to switch between pdflatex and latex using the -ini command without having to recompile the preamble.
-   File version information added.

## 17 April 2008 - Version 0.9 Build 34

-   LatexDaemon now uses UNICODE strings internally.
-   It is now possible to specify extra parameters to dvi2ps at the command prompt.
-   New command 'run' allowing the user to start a shell command. E.g. 'run cmd /c dir'
-   Two new values have been added for the afterjob option: dvipng and custom. The first will run dvipng on the output dvi file after each successful compilation. The second will run the custom command specified in the variable 'custom', passing the path to the .tex file as a parameter. For instance the options '-afterjob=custom -custom="cmd /c dir"' will list file information about the .tex document after each successful compilation of the latex document.

## 21 March 2008 - Version 0.9 Build 30

-   Bug fixed: When saving a file in certain editors like TeXnicCenter, the edited file is not modified directly; instead a temporary file is created and renamed to the desired filename. This prevented the daemon from detecting the file change. This new version now also detects this specific way of modifying files
-   Bug fixed: If the version of gsview installed is the 64bit version (under a 64bit Windows OS) then the path to gsview64.exe is not automatically detected.

## 14 March 2008 - Version 0.9 Build 28

-   New: command 'spawn' that spawns a new latexdaemon process
-   New: the executable file now contains two default icons.
-   Bug fixed: Fix a glitch in the highlighting of the latex warning messages

## 30 January 2008 - Version 0.9 Build 27

-   Bug fixed: Some handles were not closed properly.
-   Bug fixed in the function that converts absolute path to relative path (comparisons are now case-insensitive). This bug caused an infinite loop when the function was called with different capitalization in the directory name and filename, which in turn caused the watching thread to block.
-   Bug fixed: when detecting change in the dependencies, the list of dependencies was not properly updated.

## 8 October 2007 - Version 0.9 Build 24

-   Bug fixed: in raw filter mode, the latex output and the watching thread messages were interleaved.
-   Latex output filter: more information are shown about the error with the 'err' and 'err+warn' filtering mode.

## 3 October 2007 - Version 0.9 Build 23

-   New feature: automatic detection of dependencies. Use the '-autodep=no' switch to deactivate it. In this mode, the daemon detects automatically the list of files used by the main tex file and the preamble file. It then watches for changes in these files and trigger a LaTex recompilation if necessary when a modification occurs. If the dependency files reside in different directories then each of these directories will be monitored by LatexDaemon.
-   New feature: LaTex output filtering. A new command line argument '-filter={highlight|raw|err|err+warn|warn}' allows you to activate the filtering of LaTex output. The raw filter mode corresponds to the standard behaviour where the entire LaTex output is dumped to the console. In modes 'err', 'err+warn' and 'warn', the daemon filters the output so that only error and/or warnings messages appear. In 'highlight' mode, the entire output is shown with the warning and error messages highlighted.

## 29 August 2007 - Version 0.9 Build 10

-   The command 'load' used with no parameter shows up a dialog box that allows the user to choose which file to load.
-   Bug fixed when the 'preamble' command or parameter is used.
-   Internal filename comparisons are now performed case-insensitively to be compatible with FAT/NTFS conventions.
-   Bug fixed in the file monitoring loop: in particular cases, certain file modifications could be uncaught.

## 6 Mar 2007 - Version 0.9 Build 7

-   Several bugs fixed.
-   New commands at the prompt: 'vs' to view the postscript file, 'vf' to view the pdf, 'vd' for the .dvi.
-   **New** command line option: -gsview. In that mode, the .ps and .pdf viewer is forced to be gsview32 (even if the Windows file association says differently). The path to gsview is determined by fetching the default value of the key 'SOFTWARE\\Microsoft\\Windows\\CurrentVersion\\App Paths\\gsview32.exe' from the Registry. The advantage of using gsview over Adobe Reader is that gsview does not lock the file therefore the editor can be kept open during recompilation of the .tex file. Moreover, LaTexDaemon sends notification message to GSWin32 to force a refresh of the page as soon as it finishes the conversion from .dvi to .ps (this can be automated with the -afeterjob option). Consequently, you don't need to click on the Ghostview window to see the effect of your changes. This greatly improves the speed of the LaTex edit-compile-view cycle.

## 3 Mar 2007 - Version 0.9 Build 5

-   Add the @ symbol at the prompt to tell that the watching thread is currently running.
-   The watching thread can be started and stopped just by changing the value of the watch variable to yes or no.
-   The .aux file is now backed up before compilation and restored if the compilation has aborted.
     Suppose for instance that the user edits and saves the .tex file then the daemon will detect the modification and runs LaTex. If the user saves once more before the first compilation has finished then, as in previous version, the daemon will abort the first compilation. However this operation corrupts the .aux file since LaTex writes this file during the compilation. With the new version of the daemon, such situation will be recovered by restoring the backup copy of the .aux file.
-   The command line options -forcecompile and -forcefullcompile have merged into the command line option -force={compile,fullcompile}
-   Command line option -nowatch becomes -watch=no
-   New command line option -afterjob={rest|dvips} to specify a job to start after a successful compilation of the .tex file. There are just two possible values at the moment:
    -   dvips : convert the dvi file to postscript,
    -   rest: do nothing.
-   New commands available at the prompt:
    -   b\[ibtex\] to run bibtex on the .tex file
    -   c\[compile\] to compile the .tex file using the precompiled preamble
    -   d\[vips\] to convert the .dvi file to postscript
    -   e\[dit\] to edit the .tex file
    -   f\[ullcompile\] to compile the preamble and the .tex file
    -   h\[elp\] to show this message
    -   l\[oad\] file to change the active .tex file
    -   o\[pen\] to open the folder containing the .tex file
    -   p\[s2pdf\] to convert the .ps file to pdf
    -   q\[uit\] to quit the program
    -   u\[sage\] to show the help on command line parameters usage
    -   v\[iew\] to view the output file (dvi or pdf depending on the ini file)

    You can also set configuration variables with:
    -   ini=inifile set the initial format file to inifile
    -   preamble={none,external} set the preamble mode
    -   afterjob={rest,dvips} set the job executed after compilation of the .tex file
    -   watch={yes,no} to activate/desactive file modification watching

## 2 Mar 2007 - Version 0.9 Build 3

-   new feature: a command prompt interface is available while the daemon is watching for file changes. At the moment there are just 2 commands: 'f' for launching a full compilation and 'c' for a normal compilation.

## 28 feb 2007 - Version 0.9 Build 1

-   When looking for the preamble file, it first searches for a file with the same base name as the tex file but with the .pre extension, if it does not exists it then looks for preamble.tex. This allows to have several main LaTex files in the same directory each of them having its own preamble file.
-   change: option "-noextpreamble" now becomes "-preamble={none|external}:
    -   Set to 'none', it specifies that the main .tex file does not use an external preamble file. The current version is not capable of extracting the preamble from the .tex file, therefore if this switch is used the precompilation feature will be automatically desactivated.
    -   Set to 'external' (default), it specifies that the preamble is stored in an external file. The daemon first look for a preamble file called mainfile.pre, if this does not exists it tries preamble.tex and eventually, if neither exists, falls back to the 'none' option. If these files exist but do not correspond to the preamble of your LaTex document (i.e. not included with \\input{mainfile.pre} at the beginning of your .tex file) then you must set the 'none' option to avoid the precompilation of a wrong preamble.

## 26 feb 2007 - Version 0.9

-   It is now easier to prepare the .tex files to work with the daemon: it suffices to add the command \\input{preamble.tex} at the beginning of the main .tex file and to move all the code that needs to be precompiled to preamble.tex.
-   new option "-noextpreamble": specifies that the main .tex file does not use an external preamble file. This option is set by default if the file preamble.tex does not exist in the same directory as the main file. You **must** set this option if preamble.tex exists but is not included with \\input{preamble.tex} at the beginning of your .tex file. *Important note*: the current version is not capable of extracting the preamble from the .tex file, therefore if this switch is used, the precompilation feature will be automatically desactivated.

## 25 feb 2007 - Version 0.8

-   new option "-nowatch" for launching the compilation just once (if necessary) and wihtout watching for file changes.
-   new option "-forcecompile" to force an initial compilation of the .tex file even when no change is detected.
-   new option "-forcefullcompile" to force an initial full compilation (preamble and .tex file).

## 24 feb 2007 - Version 0.7

-   new option "-ini" to specify which initialization file to use when compiling the preamble. This can be use to compile a document with pdflatex instead of latex (using the parameter "-ini pdflatex").

## 9 December 2006 - Version 0.6

-   bug fixed: when computing the MD5, the file is opened with reading sharing access to ensure that the file content is not modified while computing the digest.
-   bug fixed: no more crashe when runned without parameter.

## 8 December 2006 - Version 0.5

-   new: LaTex is executed in a separate thread. This permits to interrupt and restart the compilation if a source file is modified during compilation.
-   new: the title of the console indicates if some errors occured during LaTex compilation.

## 7 December 2006 - Version 0.4

-   Change: compute the MD5 digest instead of the CRC.
-   New: it is now possible to specify additional dependency files at the command line using globling (for instance \*.tex) The first file being the main tex file.
-   New: console colors to distinguish LaTex output from the daemon output.

## 29 September 2006 - Version 0.3

-   First version released.
