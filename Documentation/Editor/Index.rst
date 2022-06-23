.. include:: /Includes.rst.txt

.. _for-editors:

===========
For Editors
===========

Prepare newsletter sending
--------------------------

There needs to be at least one Sysfolder with Doktype "Cute Mailing".
Editors can manage newsletter data via the "Newsletter" Module.

For the time being, recipient lists can only be managed via the default list
module. Select the "List" Module in the module column and then pick the
newsletter SysFolder in the page tree.

Create recipient lists first.

.. tip::

   Create one list for the normal recipients and one list with addresses for
   a test mailing.


.. figure:: /Images/Build/Recipientslist.png
   :class: with-shadow
   :alt: Add new Newsletter in backend

   Create at least two recipients lists

After creating those recipient lists you can continue with your first newsletter.

Create Newsletter
-----------------

Select the "Newsletter" Module on the left and choose a page for the newsletter
in the page tree. Then click the [+] button.

.. figure:: /Images/Editor/NewsletterModule.png
   :class: with-shadow
   :alt: Create new Newsletter from the current page

   Create new newsletter

The fields in the wizard are prefilled with the default data you entered in
Page TSconfig. You can change the target page and other data for the newsletter
in the wizard. The fields have a description and should be self-explanatory.

Once you have filled in all the necessary information you can save the
newsletter. It will not yet be released for dispatch.

Newsletter sending
------------------

Click the "send" button in the Newsletter module to trigger sending.
After clicking the "send" Button, a new newsletter task will be created.
When this newsletter task is executed it will unfold in many mail tasks, depending
on the number of recipients. You can find them in the backend module "Task Queue".
For automatic execution of such tasks, see :ref:`configuration_scheduler_task`.
Alternatively you can execute the tasks manually for testing purposes, of course.

.. figure:: /Images/Build/ModuleTaskQueue.png
   :class: with-shadow
   :alt: Module Task Queue with newsletter and mail tasks

   Module Task Queue
