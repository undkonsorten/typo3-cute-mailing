.. include:: /Includes.rst.txt

.. _for-editors:

===========
For Editors
===========

Prepare newsletter sending
--------------------------

There should be at least on Sysfolder with doktype "Cute Mailing" exists.
Editors can manage Newsletter data with the "Newsletter Module".

For the moment recipient lists can only managed via the default List module.
Select the "List Module" in the module column on the left and then select
the newsletter SysFolder in the page tree.

First create recipient lists.

.. tip::

   Create one list for the normal recipients and one list with addresses for
   a test mailing.


.. figure:: /Images/Editor/ReceiverList.png
   :class: with-shadow
   :alt: Add new Newsletter in backend

   Create at least two recipients lists

After creating that recipient lists you can create your first newsletter.

Create Newsletter
-----------------

Select the "Newsletter Module" on the left and choose a page for the newsletter
in the Page Tree. Then click the + button.

.. figure:: /Images/Editor/CreateNewsletter.png
   :class: with-shadow
   :alt: Create new Newsletter from the current page

   Create new newsletter

The fields in Wizard are prefilled with some default data. You can change target page and the other data for the newsletter in the wizard.
The fields have a description and are actually self-explanatory.

Once you have filled in all the necessary information, you can save the
newsletter. It will not yet be released for dispatch.

Newsletter sending
------------------

After clicking the "send" Button, a new newsletter task will be created. If they will be executed, new mail tasks for sending will be created. You find them in the Backend Modul "Task Queue". For automatic execution of such tasks you
should create a "Scheduler Task". Of course you can execute the tasks manually for testing.

Create a new Scheduler Task
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Choose "Execute Console Commands" in the field "Class" in Scheduler Task.
Type schould be "recurring" and set the frequency you need.
After saving the new task you can select the "taskqueue:run-tasks" as the "Schedulable Command". Set the limit for "how many task should be executed in one run" and save the task.

Now you are ready for sending Newsletters. Scheduler will start executing the
prepared Tasks in the "Task Queue".

