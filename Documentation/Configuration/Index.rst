.. include:: /Includes.rst.txt

.. _configuration:

=============
Configuration
=============

There is no special TypoScript configuration needed.
You need the Newsletter Sysfolder with Doktype "Cute Mailing".

.. figure:: /Images/Doktype.png
   :class: with-shadow
   :alt: Created Sysfolder with doktype "Cute Mailing"

   Create Sysfolder with doktype "Cute Mailing"

Then you can add Page TS-Config for some default settings in page properties
or include Page TS from your site package.

Typical example
===============

.. code-block:: typoscript

   mod.web_modules.cute_mailing {
      sender=newsletter@undkonsorten.com
      sender_name=Undkonsorten
      reply_to=newsletter@undkonsorten.com
      reply_to_name=Undkonsorten
      # we need a page type to render html code optimized for e-mails
      page_type_html=10
      # and we need a page type to render text e-mails
      page_type_text=11
      # allow marker to be replaced in rendered page
      allowed_marker=firstName,lastName,registeraddresshash
   }

