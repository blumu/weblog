:: Syntax:
::   forwardsearch.cmd maintexfilepath srcfile linenumber columnnumber
:: Editor configuration strings:
::   notepad++
::     forwardsearch.cmd $(FULL_CURRENT_PATH) $(FILE_NAME) $(CURRENT_LINE) $(CURRENT_COLUMN)
::

@echo Main TeX file: %1
@echo Source file: %2
@echo Line number: %3
@echo Column number: %4
set DDECOMMAND=[ForwardSearch("%~d1%~p1%~n1.pdf","%2",%3,%4,0,0)]
echo %DDECOMMAND% | ddeclient SUMATRA control
::pause




:: Instruction for notepad++
:: - download the files ddeclient.exe and forward.cmd to a local folder (e.g. C:\tools)
:: - close Notepad++
:: - run "notepad %AppData%\Notepad++\shortcuts.xml"
:: - add the following lines under <UserDefinedCommands> replacing 'C:\tools' with your own path.
::        <Command name="SumatraPDF" Ctrl="no" Alt="yes" Shift="no" Key="120">C:\tools\SumatraPDF-TeX.exe -reuse-instance $(CURRENT_DIRECTORY)\$(NAME_PART).pdf</Command>
::        <Command name="Forward to PDF" Ctrl="no" Alt="no" Shift="no" Key="120">C:\tools\forwardsearch.cmd $(FULL_CURRENT_PATH) $(FILE_NAME) $(CURRENT_LINE) $(CURRENT_COLUMN)</Command>
:: - save and start notepad++, you can now use Alt+F9 to start SumatraPDF and F9 to forward search to SumatraPDF from a .tex file.

:: For inverse search (SumatraPDF->notepad++)
:: Set the following inverse search string in SumatraPDF:
::  c:\Program Files\Notepad++\notepad++.exe -n%l "%f"