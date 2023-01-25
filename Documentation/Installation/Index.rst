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

.. _installation_suggested:

Suggested Recipientlists
-------------------------

If you want to use tt_address data for recipients, we suggest one of those
connector extensions.

.. container:: row m-0 p-0

   .. container:: col-md-12 pl-0 pr-3 py-3 m-0

      .. container:: card px-0 h-100

         .. rst-class:: card-header h3

            .. rubric:: `Connector Extension: cute_mailing_registeraddress <https://extensions.typo3.org/extension/cute_mailing_registeraddress>`__

         .. container:: card-body

            Use this connector if you use registeraddress extension for
            newsletter subscribing.
            registeraddress adds some new properties to the
            tt_address Model. Without those connector extension it is **not** possible
            to replace such markers in mail content.

         .. container:: card-footer

            .. code-block:: bash

               composer require undkonsorten/typo3-cute-mailing-registeraddress


   .. container:: col-md-12 pl-0 pr-3 py-3 m-0

      .. container:: card px-0 h-100

         .. rst-class:: card-header h3

            .. rubric:: `Connector Extension: cute_mailing_ttaddress <https://extensions.typo3.org/extension/cute_mailing_ttaddress>`__

         .. container:: card-body

            Use this connector if you just using tt_adress data for recipient lists.
            This connector can also be used if you use an extension that simply use
            the tt_address table for recipient data.

         .. container:: card-footer

            .. code-block:: bash

               composer require undkonsorten/typo3-cute-mailing-ttaddress

.. tip::

   Cute Mailing comes with a simple recipient list which is ready to use after
   installing the extension.
   It's just a line separated list in a textfield. Can be used for a
   Test-Recipients list or if you won't install any connector extension.
