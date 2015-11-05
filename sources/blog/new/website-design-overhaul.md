<script type="text/javascript">
var metadata = { 
    entryId : 'website-design-overhaul',
    postDate : '2015-11-05T02:11:00-08:00',
    blogVersion : 2
};
</script>

# Website design overhaul

**Wednesday, 5 November 2015, 2:11am**

_Posted by William Blum_

About 17 years have passed since I created this website. Because I have a thing 
for prime numbers I decided to celebrate this special anniversary with a design refresh.
In the process I took the opportunity to perform a full overhaul of the technologies used to power the site. 

First thing first: good bye to you [Pretty Hideous Programming language](http://php.net/), my site is now
powered by a [serious programming language](http://fsharp.org/) and relies on 
[a wonderful library](https://github.com/tpetricek/FSharp.Formatting/) for the HTML rendering.
I converted the entire site content to [markdown](https://en.wikipedia.org/wiki/Markdown) syntax
and got rid of all the unnecessary dynamic content generation code. The entire site is compiled 
statically using F# and the result pushed to an Azure Site through Git.

Back in the days I was a poor student so when Google AdSense 
was introduced it made sense (no pun intended) to use it as a mean
to pay for my development costs. However the $100/year in revenue from Google barely
 covers the _current_ web hosting cost, let alone the time spent back then maintaining this
site and developing its software. Fast forward 17 years and I now have a full time job which
means that I don't need that extra money. (Unfortunately it also means I have little time to spend
maintaining this site.) Long story short I'm proud to announce that I got rid of those pesky ads on my website :-)

Next I got rid of the [simplephpblog](http://sourceforge.net/projects/sphpblog/) blogging system.
Using the local file system to write posts, comments, and page hits on a web server seems like 
a bad idea anyway and it was causing sync issues with Git-based deployments.
I replaced the commenting part of the blog with [disqus](https://williamblum.disqus.com/). 
In the process I wrote a [Powershell script](https://github.com/blumu/weblog/blob/master/sources/blog/legacy/simplephpblob_to_wxr.ps1)
to export content from any simplephpblog blog into [Wordpress](https://wordpress.com/) WXR format.
I then used it to migrate all comments from the old blog into the new [disqus](https://disqus.com) comment system.
As for the page hit counters they are just gone; One day if I have some spare time I may reimplement page counters 
using some external telemetry system like Google Analytics.

Last but not least I got special help from [artist in residence](http://qianhanlin.blogspot.com/) Qianhan Lin (浅寒) who 
designed the wonderful logos in the top menu and helped improve the overall theme of the site. Hope you enjoy 
this new look and feel!

For posterity, and to let you fully appreciate the improvements I kept a
 [screenshot of the old wesbite](oldwebsite_cracklock_ad.png). 

The sources for this website are accessible in [GitHub](https://github.com/blumu/weblog). Feel free to take a look and reuse
them as you see fit. I welcome your feedback in the comment section below!