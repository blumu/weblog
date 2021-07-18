Cracklock
=========

- layout: cracklock
- title: Cracklock
- tags: Windows, Cracklock
- description: Cracklock for Windows

-----------------------------------------------

-----------------------------------------------
<img style="float: left; margin-right:5px;" src="cracklock.png">

<script text="text/javascript" src="latestVersion.js" > </script>

Cracklock is a unique tool that protects you from a particularly vicious polymorphic virus
affecting an ever-growing number of shareware programs.
This virus, known as the "30th day virus", typically manifests itself 30 days after the installation of the
infected software at which point it prevents it from running. Often a warning message accompanies this suspicious behaviour.
Cracklock cures your programs using cutting-edge technology that are still unknown to other anti-virus vendors (McAfee, Norton,
Sophos, Thunderbyte, F-Proot...).

<img style="float: right;" src="cracklock-config-tabs.gif">

Software developers have also used it to certify their
applications for "Year 2000" compliance. Since then we've improved Cracklock and are proud to announce that it can also help certify your software against the Y10K bug (aka "bug of the year 10000"). Thanks to Cracklock those bugs have all become problems of the past.

Lastly Cracklock has a [feature](changelog-web/) specifically
designed to work around the
["Microsoft Outlook timezone bug"](http://www.google.com/search?hl=en&q=outlook%20timezone%20problem).

<ul class="home-download os_windows">
  <li class="os_windows">
  <script>document.write('<a href="downloads/' + latest_setupfile +'" class="download-link download-cracklock">');</script>
  <span><strong>Download Cracklock</strong>
  <em>Latest version <script>document.write(latest_version);</script>&nbsp;(1.27MB)</em></span>
  </a>
  </li>
</ul>
<div class="download-other"><span class="other">
  <a href="changelog-web/">Change logs</a>-
  <a href="cracklock-webfaq/">Faq</a>-
  <a href="xmldoc/cracklock-doc-web/">Documentation</a></span>
</div>


<div style="float:right; margin-left:15px">
  <script type="text/javascript"><!--
google_ad_client = "pub-7250791356906762";
//336x280, date de cr?ation 20/01/08
google_ad_slot = "5772791401";
google_ad_width = 336;
google_ad_height = 280;
//--></script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>

What's new
----------

See the [change log](changelog-web/) to see the list of recent changes.
The following features were introduced since version 3.9:

- Cracklock can now be run from a flashdisk
- You can choose whether to store settings in the Windows Registry or in an INI file.
- Translation in the following languages: French, English, Spanish, Arabic, Serbo Croatian, Hungarian, Korean, German, Simplified chinese and Portuguese.


Frequently Asked Questions
--------------------------

A Cracklock FAQ is available [here](cracklock-webfaq/).

Google Groups
-------------

For discussions about Cracklock, visit [the Cracklock group.](http://groups.google.com/group/cracklock)

Documentation 
-------------

The english documentation is available [here](xmldoc/cracklock-doc-web/).
For other languages an older version of the documentation is available:

- ![Help](images/help.gif) [English](help/english/index.html)
- ![Help](images/help.gif) [Spanish](help/spanish/index.html)
- ![Help](images/help.gif) [French](help/french/index.html)
- ![Help](images/help.gif) [Arabic](help/arabic/index.html)

Contributing to the translation
-------------------------------

The translation tool I once developed ([RLGui](../others/index.html#rlgui)) is now deprecated.
Although it had nice features (for instance it can automatically reuse strings translated for version *n* of a
program to generate translation for version *n+1*), it required some postprocessing every time a translator
submitted translated strings.

Going forward, you can author new translations of Cracklock using any resource file editor available on the net.
I recommend [Resource Hacker](http://www.users.on.net/johnson/resourcehacker/),
[Resource Explorer](http://www.wilsonc.demon.co.uk/d7resourceexplorer.htm), or Microsoft Visual Studio.
You can use those tools to edit any file under the `Language` subdirectory of Cracklock 
(e.g. `CLRESUS.DLL` for the English version) then save it under another name
(e.g. `CLRESGE.DLL` for German) in the same directory.
After restarting Cracklock Manager, the newly created language will appear under the Language menu.

![Download previous versions](../cracklock/images/download.gif) Previous versions
---------------------------------------------------------------------------------
