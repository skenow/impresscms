===============================================================================
 xajax PHP & Javascript Library
 The easiest way to develop powerful Ajax applications with PHP

 Version 0.5 (Beta 2)
 README Text File
 January 26, 2007

 ------------------------------------------------------
 | ** Release Notes:                                  |
 | See release_notes.txt in this download archive     |
 |                                                    |
 | ** Project Managers:                               |
 | Jared White (jared@intuitivefuture.com)            |
 | J. Max Wilson (jmaxwilson@users.sourceforge.net)   |
 |                                                    |
 | ** Developers:                                     |
 | Eion Robb (eion@bigfoot.com)                       |
 | Joseph Woolley (joe@calledtoconstruct.net)         | 
 ------------------------------------------------------
===============================================================================

 :: To find out what's changed since the 0.5 Beta 1 release of xajax, ::
 :: view the Release Notes in the file listed above.                  ::

The Beta 2 release of xajax 0.5 is a big step forward for the project, thanks
to a major contribution of a brand-new Javascript engine by our new developer
Joseph Woolley (CtC). It took us a while to refine the design and ensure
compatibility with all of the various xajax features we needed to implement,
but we think you'll agree it was worth the wait.

Beta 2 marks the beginning of stabilization of the 0.5 API on both the PHP and
Javascript side. Any changes we make from here on out leading up to the final
0.5 release will be small, unless there is an overwhelming and widely
recognized reason for a large modification. Basically, you can start to design
and test applications using 0.5 Beta 2 with a reasonable degree of confidence.

We're working hard to bring the documentation up to snuff with the new Beta.
In the meantime, we encourage you to visit our forums at
http://community.xajaxproject.com and ask for help and give us your feedback.
You can also report bugs you find at http://www.sourceforge.net/projects/xajax
using our bug tracker.

Thank you for trying out xajax 0.5 Beta 2! We hope you like it!

____________________________________________________________________

1. Introduction

xajax is a PHP library that you can include in your PHP scripts
to provide an easy way for Web pages to call PHP functions or
object methods using Ajax (Asynchronous Javascript And XML). Simply
register one or more functions/methods with the xajax object that
return a proper XML response using the supplied response class, add
a statement in your HTML header to print the Javascript include,
and run a request processor prior to outputting any HTML. Then add
some simple Javascript function calls to your HTML, and xajax takes
care of the rest!

xajax includes a Javascript object to facilitate the communication
between the browser and the server, and it can also be used as a
Javascript library directly to simplify certain DOM and event
manipulations. However, you can definitely choose to use a
dedicated Javascript "engine" of your liking and integrate it with
xajax's client/server communication features. Since xajax is moving
towards a highly modular, plugin-based system, you can alter and extend
its behavior in a number of ways.

2. For More Information

The official xajax Web site is located at:
http://www.xajaxproject.org

Visit the xajax Forums at:
http://community.xajaxproject.org
to keep track of the latest news and participate in the community
discussion.

There is also a wiki with documentation, tips & tricks, and other
information located at:
http://wiki.xajaxproject.org

3. Installation

To run xajax, you need:
* Apache Web Server or IIS for Windows XP/2003 Server
   (other servers may or may not work and are not supported at this
   time)
* PHP 4.3.x or PHP 5.x
* Minimum supported browsers: Internet Explorer 5.5, Firefox 1.0 (or
   equivalent Gecko-based browser), Safari 1.3, Opera 8.5 (older
   versions only work with GET requests)

To install xajax:
Unpack the contents of this archive and copy them to your main Web
site folder. Or if you wish, you can put all of the files in a
dedicated "xajax" folder on your Web server (make sure that you
know what that URL is relative your site pages so you can provide
xajax with the correct installed folder URL). Note that the
"thewall" folder in the "examples" folder needs to be writable by
the Web server for that example to function.

Within the main xajax folder there are four folders: "examples",
"tests", "xajax_js", and "xajax_core". Only "xajax_js" and
"xajax_core" are required to use xajax.

You should be able to view the PHP pages in "tests" from your
Web browser and see xajax working in action. If you can view the
pages but the AJAX calls are not working, there may be something
wrong with your server setup or perhaps your browser is not
supported or configured correctly. If worst comes to worst, post
a message in our forums and someone may be able to help you.

4. Documentation

** NOTE: out-of-date docs will be updated for Beta 2 over the
coming days! **

Detailed documentation for the xajax PHP classes is available on
our wiki (URL listed above in section 2), and more is on the way
(particularly in regards to the Javascript component of xajax).
Another good way of learning xajax is to look at the code for the
examples and tests. If you need any help, pop in the forums and
ask for assistance (and the more specific your questions are,
the better the answers will be).

5. Contributing to xajax

xajax is released under the BSD open source license. If you wish
to contribute to the project or suggest new features, introduce
yourself on the forums or you can e-mail the project managers
and developers at the addresses listed at the top of this README.

6. Good luck and enjoy!

====================================================================
