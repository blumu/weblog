@rem This line serves to eat the BOM (byte-order-mark, present if the cmd file is saved by Notepad in UTF-8 mode)
@echo OFF
rem Created by William Blum on 22 fev 2006.
rem Acknowledgement: DanielThibault for his suggestions.
rem Install a truetype unicode font for latex and pdflatex
rem requirement:
rem   - ttf2tfm, ttfucs.sty and utf8ttf.def

echo Truetype font installer for Latex by William Blum.
echo Version 1.2 - 13/11/2007
echo.

setlocal

if (%1)==() goto print_syntax
set TFFFILE_PATH=%1
set TFFFILE_BASENAME=%~n1
set TFFFILE_EXT=%~x1
rem Short name for the font (6 characters maximum) used to generate the files "baseXX.enc"
set TFFFILE_SHORT_BASENAME=%TFFFILE_BASENAME:~0,6%

set TEXMF=%2
if (%TEXMF%)==() goto detect_miktex_dir

echo The font will be installed in the directory: %TEXMF%
goto get_pideid

:detect_miktex_dir
rem Guess miktex dir: look for a directory in the PATH env var containing pdflatex.exe.
FOR %%I IN (pdflatex.exe) do set TEXMF="%%~$PATH:I"

rem default value
if (%TEXMF%)==("") set TEXMF="C:\Program files\Miktex 2.7" & goto miktex_dir_ok
set TEXMF=%TEXMF:\miktex\bin\pdflatex.exe=%
echo MiKTex directory detected: %TEXMF%
goto get_pideid

:get_pideid
set PID=%3
rem default value
if (%PID%)==() set PID=3

set EID=%4
rem default value
if (%EID%)==() set EID=1


rem Check that the font file extension is either .ttf or .ttc (/i for case insensitive comparison)
IF /i %TFFFILE_EXT% == .ttf goto ttfextension_ok
IF /i %TFFFILE_EXT% NEQ .ttc goto badfileextension
:ttfextension_ok
IF NOT EXIST %TFFFILE_PATH% goto filenotfound
IF NOT EXIST %TEXMF% goto dirnotfound


echo Install the truetype font "%TFFFILE_BASENAME%%TFFFILE_EXT%" in the latex directory %TEXMF% ...

@echo ON
@if not exist %TEXMF%\fonts\truetype\ mkdir %TEXMF%\fonts\truetype\
copy %TFFFILE_PATH% %TEXMF%\fonts\truetype\
cd %TEXMF%\fonts\truetype
ttf2tfm %TFFFILE_BASENAME%%TFFFILE_EXT% -P %PID% -E %EID% -w %TFFFILE_SHORT_BASENAME%@Unicode@ > ttffortex_%TFFFILE_BASENAME%.log ||  goto ttf2tfm_error

@if exist %TFFFILE_BASENAME%.map del %TFFFILE_BASENAME%.map
for %%i in (*.enc) do @echo %%~ni ^<%TFFFILE_BASENAME%%TFFFILE_EXT% ^<%%~ni.enc >>%TFFFILE_BASENAME%.map
@if not exist %TFFFILE_BASENAME%.map goto nomap

@if not exist %TEXMF%\pdftex\config\ mkdir %TEXMF%\pdftex\config\
move %TFFFILE_BASENAME%.map %TEXMF%\pdftex\config\

@if not exist %TEXMF%\fonts\tfm\%TFFFILE_BASENAME% mkdir %TEXMF%\fonts\tfm\%TFFFILE_BASENAME%
move %TFFFILE_SHORT_BASENAME%*.tfm %TEXMF%\fonts\tfm\%TFFFILE_BASENAME%\

@if not exist %TEXMF%\pdftex\enc\%TFFFILE_BASENAME% mkdir %TEXMF%\pdftex\enc\%TFFFILE_BASENAME%
move %TFFFILE_SHORT_BASENAME%*.enc %TEXMF%\pdftex\enc\%TFFFILE_BASENAME%\

@rem Update the Truetype font map file (ttfonts.map) used by ttf2pk to generate the pk bitmap files representing the glyphs of the truetype font.
@rem (the PK files are used by the DVI viewer and by dvi2ps)
echo %TFFFILE_SHORT_BASENAME%@Unicode@  %TFFFILE_BASENAME%%TFFFILE_EXT%    Pid=%PID% Eid=%EID% >> %TEXMF%\ttf2tfm\base\ttfonts.map

@if not exist %TEXMF%\tex\latex\winfonts mkdir %TEXMF%\tex\latex\winfonts
@set FDFILE=%TEXMF%\tex\latex\winfonts\C70%TFFFILE_BASENAME%.fd
@echo \ProvidesFile{C70%TFFFILE_BASENAME%.fd}[%TFFFILE_BASENAME%] > %FDFILE%
@echo \DeclareFontFamily{C70}{%TFFFILE_BASENAME%}{\hyphenchar \font\m@ne} >> %FDFILE%
@echo \DeclareFontShape{C70}{%TFFFILE_BASENAME%}{m}{n}{^<-^> CJK * %TFFFILE_SHORT_BASENAME%}{} >> %FDFILE%
@echo \DeclareFontShape{C70}{%TFFFILE_BASENAME%}{bx}{n}{^<-^> CJKb * %TFFFILE_SHORT_BASENAME%}{\CJKbold}	>> %FDFILE%
@echo \endinput >> %FDFILE%

initexmf --update-fndb

@goto succeed

:print_syntax
@echo off
echo The syntax is:
echo. %0 ttffile [texmfdir] [pid] [eid]
echo  . ttffile is the full path to the true-type font (e.g. c:\WINDOWS\Fonts\simhei.ttf)
echo  . texmfdir is the local root latex directory (where you want to install the font; if no argument is provided then it uses the Miktex directory defined in the PATH environment variable; do NOT use a relative path)
echo  . pid is the platform id of the font (used by ttf2tfm)
echo  . pid is the encoding id of the font (used by ttf2tfm)
echo.
echo Example:
echo   %0 C:\Windows\Fonts\Simkai.ttf
echo.
goto end

:filenotfound
@echo off
echo File "%TFFFILE_PATH%" not found!
SET v_return=1
goto end

:badfileextension
@echo off
echo Bad file extension ("%TFFFILE_PATH%")! The font file must have the .TTF or .TCC extension.
SET v_return=4
goto end

:dirnotfound
@echo off
echo The directory "%TEXMF%" does not exist!
SET v_return=2
goto end

:ttf2tfm_error
@echo off
echo Problem during the execution of ttf2tfm. Try to specify parameter PID and EID.
SET v_return=3
goto end

:nomap
@echo off
echo Problem during the generation of the map file.
SET v_return=4
goto end

:succeed
@echo off
echo.
echo Operation succeeded. The truetype font has been installed to your LaTeX environment!
echo You can use it in an TeX document as follows:
echo   \usepackage[utf8ttf]{inputenc}
echo   \usepackage{ttfucs}
echo   \DeclareTruetypeFont{%TFFFILE_BASENAME%}{%TFFFILE_SHORT_BASENAME%}
echo   \begin{document}
echo   \TruetypeFont{%TFFFILE_BASENAME%}
echo   unicode texte
echo   \end{document}
SET v_return=0


:end

endlocal

rem Utilisation sous pdflatex:
rem necessite les fichiers ttfucs.sty et utf8ttf.def

rem \usepackage[utf8ttf]{inputenc}
rem \usepackage{ttfucs}
rem \DeclareTruetypeFont{cyberb}{cyberb}
rem \DeclareTruetypeFont{simkay}{simkay}

rem \begin{document}
rem \TruetypeFont{cyberbit}
rem 中文 大
rem \TruetypeFont{simkai}
rem 日本小
rem \end{document}