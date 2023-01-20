.. include:: /Includes.rst.txt

.. _installation:

============
Installation
============

The extension needs to be installed as any other extension of TYPO3 CMS:

Get the extension by one of the following methods:

Install the extension with Composer
-----------------------------------

.. code-block:: bash

   composer require undkonsorten/cute-mailing

Get it from the Extension Manager
---------------------------------

Switch to the module Admin Tools > Extensions.
Switch to Get Extensions and search for the extension key cute_mailing and
import the extension from the repository. *Please note that this installation
method is not tested. Please feedback your experiences!*

Latest version from git
-----------------------
You can get the latest version from git by using the git command:


.. code-block:: bash

   git clone https://github.com/undkonsorten/typo3-cute-mailing

Requirements
------------


Cute Mailing requires the extension `taskqueue <https://github.com/undkonsorten/taskqueue>`__
for sending the emails.
The extension is automatically installed during installation via Composer.
If Cute Mailing is installed via one of the other ways, the extension must be
installed manually before.

Recipient lists
---------------

By default the extension comes with a line seperated recipient list.
But there are extension that introduce different recipient list for
`tt_address <https://github.com/FriendsOfTYPO3/tt_address>`__ or
`registeraddress <https://github.com/lsascha/registeraddress>`__ :

* `undkonsorten/typo3-cute-mailing-ttaddress <https://github.com/undkonsorten/typo3-cute-mailing-ttaddress>`__
* `undkonsorten/typo3-cute-mailing-registeraddress <https://github.com/undkonsorten/typo3-cute-mailing-registeraddress>`__
