.. include:: /Includes.rst.txt

.. _update:

======
Update
======

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


