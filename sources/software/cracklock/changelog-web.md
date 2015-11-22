@{
  Layout = "page";
  Title = "Cracklock Change Log";
  Tags = "Cracklock";
  Date = "";
  Description = "Cracklock Change Log";
}
<span id="id256149"></span>Cracklock Change Log
-----------------------------------------------

<span class="term">3.9.44 - 16 March 2008 </span>

-   Fixed: The setup does not set write permissions for the Cracklock.settings file, so if the user chooses to store settings in cracklock's directory then it is impossible to add applications to Cracklock.
-   Setup: If some files that need to be updated are in use then it shows a dialog box listing all the applications that need to be stopped before proceeding with the installation/uninstallation.

<span class="term">3.9.42 - 11 March 2008 </span>

-   <span class="bold">**Important bug fixed:**</span> Cracklock did not work in parts of the world where no daylight saving scheme is implemented! (These include the following countries: Afghanistan, Bahrain, China, Hong Kong, Japan, Kazakhstan, Kyrgyzstan, Tajikistan, Turkmenistan, Uzbekistan, Macau, Mongolia, North Korea, Qatar, Kuwait, Saudi Arabia, South Korea, Nepal, Bhutan, Pakistan, India, Bangladesh, Myanmar, Taiwan, Thailand, Laos, Vietnam, Philippines, Cambodia, Malaysia, Brunei, Singapore, Indonesia, Timor Leste, Papua New Guinea, Venezuela)

-   Chinese translation updated. This may become more useful now that Cracklock works in China ;-)

-   Fixed: the default parameters for the control panel date and time applet was incorrectly set during installation.

-   Fixed: small bugs fixed in the Manager.

-   Manager: Hitting the F5 key refreshes the list

-   Examples: new clock example written in C\#. The VCDate and VBDate have been renamed into C Clock Example and VB Clock Example

<span class="term">3.9.38 - 9 March 2008 </span>  

-   New: Vista is officialy supported

-   New: Support for flashdisk instllation

-   New: Improvements of the user interface.

-   New: a manifest file has been added to ensure that version 6.0 of the Windows Common controls DLL is used in order to avoid the annoying beep happening on item selection under Windows Vista.

-   New: The manager now allows you to configure a command line argument for each application. It will be appended to the command line when the application is runned from within the manager.

-   New: per-application setting called "stand-alone mode". In the normal mode the behaviour is the same as before. In full stand-alone mode, the settings and the injected DLL are copied to the program folder. In a third "in-between" mode, only the settings are stored in the application folder. When adding a new application, if the file App.exe.cracklock is present in the directory then default settings are loaded from this file. It is deleted when when the application is removed from cracklock.

-   New: option to let the user change the command parameters that are used to start the application from the Manager.

-   New: option in the general settings: Shared/Stand-alone installation. In shared mode, Cracklock's directory is added to the PATH environment variable. In stand-alone mode, the injected DLL is copied to each application's folder.

-   New: The manager now contains a manifest to ensure that version 6.0 of the Windows Common controls DLL is used in order to avoid the annoying beep happening on item selection under Windows Vista.

-   New: an option in the general settings allows to set the default injection mode when adding new applications to Cracklock.

-   New: an option that allows one to set the location of the general settings (Registry or INI file). It is also possible to save the application settings in each application's directory (this setting can be overwritten on a per-application basis).

-   New: a description can be assigned for each application listed in the manager.

-   New: The documentation is now generated from an XML DocBook file.

-   Bug fixed: remove a spurious message reporting that CLKERN.DLL is in use when trying to delete it from an application folder.

-   Bug fixed: In some very particular cases, some API calls were properly hooked.

-   Bug fixed: The loader now accepts relative paths.

-   Setup: There is now a new 'Flashdisk installation' mode. In this mode, nothing is written in the Windows Registry and shortcuts are not created.

-   Setup: a new option let you choose where you want to store the setting file (registry, application data folder or cracklock's folder).

-   Setup: import the settings from the registry to the INI file if the user choose to store settings in an INI file.

-   Change: Definition file CLKERN.def regenerated with the latest version of Kernel32.dll

-   Change: Date mode selected by default when adding a new app.

-   Change: Icons in the Manager are now shown in 32bits colors.

<span class="term">3.8.27 - 1st December 2007</span>  

-   New feature: in the loader-based injection mode, the loader is now more furtive: the application cannot detect that it is being debugged.
-   Fixed: When the import table of a PE file contained more than one occurrences of the module KERNEL32.DLL, the PE file could not be backed-up automatically by the manager.
-   Bug fix: In some circumstance, the Cracklock manager window positioned itself in the invisible part of the screen.

<span class="term">3.8.26 - 15 September 2007</span>  

-   Update: Chinese and French translation updated
-   Fixed: The setup can again be run without requiring administrator privileges
-   Fixed: applications crashed if no virtual mode (time, date or timezone) was selected.
-   Fixed: PE executable files containing a Bound Import table caused a crash of the application.
-   Fixed: Link checksum in PE files are now recalculated whenever the files are altered (this only occurs in static injection mode).
-   Fixed: Allows the resident injector mode on Windows>=NT
-   Fixed: The setup now kills the resident injector before copying files

<span class="term">3.8.20 - 11 September 2007</span>  

-   The configuration dialog has been redesigned.
-   New feature: you can create a shortcut on the desktop that will start the application with Cracklock loader. (There is a hotlink in the configuration dialog to create the shortcut).
-   New feature: there is now a Setting window available from the Manager that allows you to configure general settings for Cracklock. It contains the following options:
    -   Add/remove Cracklock to the PATH environment variable (so that an application can find CLKERN.DLL if it uses static injection)
    -   An option to copy CLKERN.dll to the folder of each application. Usefull if you use static injection and you don't want Cracklock to be in the PATH environment variable.
    -   Activate system-wide injection by launching a small application at logon that is responsible for injecting Cracklock's DLL in every process.
    -   Activate system-wide injection using AppInitDll (requires admin rights).
    -   Install/uninstall the shell-extension for Windows explorer (requires admin rights).
    -   Small bugs fixed in the manager and in the installation.

<span class="term">3.8.13 - 8 August 2007</span>  

-   New feature: a virtual timezone can be selected for each application running under Cracklock. For instance this feature allows you to solve the ["Microsoft Outlook timezone problem"](http://www.google.com/search?hl=en&q=outlook%20timezone%20problem) by selecting the appropriate timezone.
-   The configuration dialog has been redesigned (Note that some messages have not been translated in other languages yet).
-   The manager's list contains new columns showing the date, time and timezone selected for each application.
-   Fixed: the loader injection method (MCL.EXE) now works again. With this method, it is not necessary to alter any executable file to activate Cracklock.
-   Fixed: the loader MCL.exe now parses correctly its command line parameters. It is now possible to load an application with optional command line parameters with the syntax "MCL.EXE APP.EXE APP\_PARAMETERS"

-   Fixed: the system-wide option (when the system time is changed for real instead of being virtualized) did not work properly.

-   Several minor bugs have been fixed.

-   Setup: new option to decide whether to install a system-wide injector resident in memory (CLINJECT.exe launched at the logon or CLKERN.DLL loaded as an AppInit DLL)

-   Update: CLKERN.dll has been updated to take into account new win32 kernel functions that have been introduced in recent Windows updates

-   Internal: PEDUMP replaced by DUMPBIN in the toolchain.

-   A new example is now added automatically to the list of managed applications: it allows you to test Cracklock on the Windows control panel date/time applet.

<span class="term">3.8.9 - 2 August 2007</span>  

-   Portuguese language added. Thanks to Marcelo Schneider for the translation.

-   Internal: the Makefiles that were used to compile the sources are now replaced by a Visual Studio project. MSBuild is now used instead of GNU make.

-   Internal: the new "secure" versions of the string functions from the standard C library are now used. This protects Cracklock from potential buffer overflow attacks.

<span class="term">3.8.8 - 7 November 2005</span>  

-   Fixed: the setup now proposes to not install component that need administrator rights (like explorer shell extensions)

-   Fixed: applications configurations are now stored at the user level instead of the machine level (this permits to use Cracklock on a public computer)

-   Internal fix: the make build system is used in place of the Perl script files. Visual Studio Toolkit 2003 is used to compile the C++ sources.

<span class="term">3.8.7 - 16 September 2005</span>  

-   Fixed : imcompatibility with Windows 2000/NT (function TzSpecificLocalTimeToSystemeTime)

<span class="term">3.8.6 - 17 July 2005</span>  

-   Fixed: Chinese and spanish translation

-   Fixed: size of tab sheet title

-   Fixed: keyboard shortcurs in the manager

-   Feature: drag & drop is now allowed in the manager

-   Fixed: selecting a language un the manager now affects the shell extension with no need to restart the Windows explorer

-   Fixed: controls position in the translated shell extension tab sheets

<span class="term">3.8.5 - 8 May 2005</span>  

-   Converted entirely into UNICODE
-   UI bugs fixed
-   The "freezed date" option now works
-   Help converted into .CHM format
-   Bugs related to daytime saving management have been fixed
-   Languages added: Hungarian, Korean, German and Simplified Chinese
-   Multilingual installation
-   A very rare bug happening during the uninstallation has been fixed
-   The uninstallation now completely removes keys created by Cracklock

<span class="term">3.8.4 - 2 August 2001</span>  

-   Windows 2000/Xp compatibility improved

<span class="term">3.8.3 - 25 July 2001</span>  

-   Arabic and Croatian translation

<span class="term">3.8.2b - 20 January 2001</span>  

-   less bug than before

<span class="term">3.8.1 - 17 September 1999</span>  

-   Setup: You can now use the manager !

<span class="term">3.8 - 27 August 1999 </span>  

-   Kernel: New kernel system. Now, it is possible to act onto an application without modifying its EXE file. And you still can start your program from anywhere (DOS box, explorer, Cracklock Manager).
-   Setup: Cracklock Shell Extension are now installed correctly.
-   Shellext/Manager: Now, the selected file in the dependency list can be unselected without being grayed.

<span class="term">3.7.1 - 5 August 1999</span>  

-   Manager: A bug in 3.7 avoided to add new application to the manager list.

<span class="term">3.7 - 16 July 1999</span>  

-   Some modifications in the manager.
-   Three languages are included (English, Spanish, French).
-   Correction of a bug which avoid the recognition of application referenced by a short file name.

<span class="term">3.6 - 3 January 1999</span>  

-   A bug avoiding two carcklocked programs to run together under Win95 is fixed.
-   The general layout of the help files has been modified.

<span class="term">3.5 - 25 December 1998</span>  

-   New install ans uninstall system
-   Cracklock manager
-   Multilingual support: choice of the foreign language in the manager menu
-   Contextual HTML help
-   Kernel : improvements under NT
-   ShellExt : re-using of backup file when it exists instead of modifying DLL (with original date preserved after the file backup)
-   ShellExt : in the explorer, the contextual menu which launches an app in loader mode is shown only if necessary
-   Loader: when apps are executed in loader mode, Cracklock enters the debug mode only if necessary
-   Kernel: updates the list of DLL's each time a new DLL is loaded

<span class="term">3.0 - 2 August 1998</span>  

-   With the new option "Loaded by Cracklock Loader", there is no more dependencies problems. Cracklocks requires only one DLL to be modified when set in the "normal" mode; it does not modified any DLL when set in "loader" mode.
-   The reboot dialog box includes a button to cancel the operation
-   The reboot process is supported under Windows NT
-   Long File Names are preserved when files are modified after a reboot
-   BUG fixed: the list of files which can't be modified is no more empty.

<span class="term">2.3 - 18 june 1998</span>  

-   A big bug has been removed. Thank to Paul Widup who discovered this bug.

<span class="term">2.2 - 15 june 1998</span>  

-   Repairs automatically VB 5 programs - When there isn't any file recognized in the dependencies list, Cracklock select, by default, the first which can be repaired.

<span class="term">2.1 - 30 may 1998</span>  

-   NEW: When a program is using files that you want to repair, Cracklock can repair them by restarting the computer.
-   ALL error messages are grouped into a unique dialog box.
-   Constant Date/Time bug fixed.

<span class="term">2.0 - 9 april 1998</span>  

-   A lot of bugs fixed

<span class="term">2.0 beta - 6 march 1997</span>  

-   Now Cracklock repairs automatically dependent DLLs such as MSVCRT.DLL and VB40032.DLL
-   AM/PM bug corrected !

<span class="term">1.1 - 21 january 1997</span>  

-   Addition to the documentation (how to repair VB & VC programs).

<span class="term">1.0 - 5 january 1997</span>  

-   Modification of the documentation.

<span class="term">1.0 - 2 December 1997</span>  

-   Now you can remove CrackLock from Windows 95 and NT. Previously, it was impossible to uninstall Cracklock under Windows 95.

<span class="term"> 1.0 beta - 20 November 1997</span>  

-   Documentation is fixed and FILE\_ID.DIZ is added.

<span class="term">1.0 alpha - 1 November 1997</span>  

First release of Cracklock.


