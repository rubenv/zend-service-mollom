This package contains the Zend Framework service for Mollom.

Mollom is a web service that analyzes the quality of content posted to
websites. This includes comments, contact-form messages, blogs, forum posts,
etc. Mollom specifically tries to determine whether this content is
unwanted - i.e. "spam" - or desirable - i.e. "ham." Websites that allow
visitors to contribute or post comments are constantly being flooded with
inappropriate, distracting or even illegal commercial messages, many of
which are uploaded by automatic "spambots." Mollom screens all
contributions before they are posted to participating websites.

RELEASE INFORMATION
-------------------

Zend_Service_Mollom 1.1.0 for Zend Framework 1.6.1 and above
Released on 2008-12-06

Changes in this version:
 - Fixed a bug where the REFRESH case wasn't handled correctly (thanks to 
   Dries Buytaert for pointing out!)
 - Add unit test coverage

Full changelog below.

SYSTEM REQUIREMENTS
-------------------

Zend_Service_Mollom requires Zend Framework 1.6.1 or later.
Previous versions might work, but have not been tested.

INSTALLATION
------------

Please see /INSTALL.txt.

QUESTIONS AND FEEDBACK
----------------------

Online documentation can be found at 
http://ruben.savanne.be/articles/zend-service-mollom-documentation.

Further questions and feedback can be sent to the author (see the
ACKNOWLEDGEMENTS section below).

LICENSE
-------

The files in this archive are released under the Zend Framework license. You
can find a copy of this license in /LICENSE.txt.

ACKNOWLEDGEMENTS
----------------

Zend_Service_Mollom was written by Ruben Vermeersch (ruben@savanne.be).
More information can be found on his website: http://www.savanne.be/

All questions, remarks & feedback are welcome, I am also available for
contracting on PHP related projects.

CHANGELOG
---------

07 Dec 2008 - 1.1.0:
 - Second release, incorporating the feedback from the Mollom team. Should be 
   ready for public consumption.
 - Fixed a bug where the REFRESH case wasn't handled correctly (thanks to 
   Dries Buytaert for pointing out!)
 - Add unit test coverage

06 Dec 2008 - 1.0.0:
 - Initial release
