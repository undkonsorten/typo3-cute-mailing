.. include:: /Includes.rst.txt

.. _update:

======
Update
======

.. _update_to_4.1:

Update to 4.1
================

TYPO3 11 compatibility has been removed and TYPO3 13 compatibilty has been added
in 4.1.0.
There are currently no known breaking changes.
The wizard to migrate old Sysfolders with the old CuteMailing doktype
(from version 1.x) has been removed. If you update CuteMailing from a version
lower than 2, you need an interim update step with version 2.x or 3.x to use
that update wizard. Or change existing folders manually.

.. _update_to_2.0:

Update to 2.x?
================

CuteMailing Sysfolder now should be marked as module. In v1.x the Sysfolder had an special doktype.
The "module" property is now used instead of the "doktype" property.
Execute :ref:`upgrade wizard <t3install:postupgradetasks>` to update old Sysfolders.

If you are using an old recipient list from v1 of CuteMailing,
then you should install undkonsorten/typo3-cute-mailing-registeraddress
or undkonsorten/typo3-cute-mailing-ttaddress (:ref:`see here <installation_suggested>`).
Use the :ref:`upgrade wizard <t3install:postupgradetasks>` to migrate the old recipient list types.


