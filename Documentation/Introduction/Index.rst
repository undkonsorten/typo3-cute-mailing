.. include:: /Includes.rst.txt

.. _introduction:

============
Introduction
============

.. _what-it-does:

What does it do?
================

Cute Mailing is a **multilanguage** newsletter tool that spools and sends TYPO3 pages as emails to
many recipients. Cute Mailing can handle different types of recipient lists.
That is possible with so called "Connector Extensions". There are
two extension that introduce different recipient list for
`tt_address <https://github.com/FriendsOfTYPO3/tt_address>`__ or
`registeraddress <https://github.com/lsascha/registeraddress>`__ :

* `undkonsorten/typo3-cute-mailing-ttaddress <https://github.com/undkonsorten/typo3-cute-mailing-ttaddress>`__ (if you just using tt_address data for recipients)
* `undkonsorten/typo3-cute-mailing-registeraddress <https://github.com/undkonsorten/typo3-cute-mailing-registeraddress>`__ (if you use tt_adress data and register recipients with the ext: registeraddress)

For testing purpose or some quick and easy newsletter sending, Cute Mailing
provides a very simple text based recipient list type. You can use that type
to create a small list in a textarea by using a new line for every recipient.
You can use known email syntax as following or only the email address:

.. code-block:: typoscript

   FirstName LastName <firstname.lastname@example.org>

Cute Mailing is heavily inspired by luxletter but omits the rendering part to
focus on it‘s main purpose:

Cute Mailing only does one thing: **Sending mails!**

We say thank you to the developers at In2Code for their good work.

.. important::

   CuteMailing does not provide HTML templates or plugins for newsletter
   subscription! There are ready-made solutions or tools that can be used.
