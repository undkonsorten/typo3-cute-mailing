.. include:: /Includes.rst.txt

.. _installation:

============
Installation
============

The extension needs to be installed as any other extension of TYPO3 CMS:

Get the extension by one of the following methods:

Use composer:

.. code-block:: bash

   composer require undkonsorten/cute-mailing

Get it from the Extension Manager
---------------------------------

Switch to the module Admin Tools > Extensions.
Switch to Get Extensions and search for the extension key cute_mailing and
import the extension from the repository.

Latest version from git
-----------------------
You can get the latest version from git by using the git command:

.. code-block:: bash

   git clone https://github.com/undkonsorten/cute_mailing.git

Requirements
------------

CuteMailing requires the extension `taskqueue <https://gitbucket.undkonsorten.com>`__
for sending the emails.
The extension is automatically installed during installation via Composer.
installed. If CuteMailing is installed via one of the other ways,
the extension must be installed manually.
