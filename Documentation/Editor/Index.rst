.. include:: /Includes.rst.txt

.. _for-editors:

===========
For Editors
===========

Prepare newsletter sending
--------------------------

There needs to be at least one Sysfolder as :ref:`Cute Mailing Module folder <configuration_setup_sysfolder>`.
Editors can manage newsletter data via the "Newsletter" Module.

**Create recipient lists first.**

For the time being, recipient lists can only be managed via the default list
module. Select the "List" Module in the module column and then pick the
newsletter SysFolder in the page tree. There you should create new data with
Type CuteMailing -> Recipient List.

.. tip::

   Create one list for the normal recipients and one list with addresses for
   a test mailing.

.. tip::

   Consider using our suggested extensions for different recipient list e.g tt_address


.. figure:: /Images/Build/Recipientslist.png
   :class: with-shadow
   :alt: Add new Newsletter in backend

   Create at least two recipients lists

After creating those recipient lists you can continue with your first newsletter.

Create Newsletter
-----------------

Select the "Newsletter" Module on the left and choose a page for the newsletter
in the page tree. Then click the [+] button.

.. figure:: /Images/Build/NewsletterModule.png
   :class: with-shadow
   :alt: Create new Newsletter from the current page

   Create new newsletter

The fields in the wizard are prefilled with the default data you entered in
Page TSconfig. You can change the target page and other data for the newsletter
in the wizard. The fields have a description and should be self-explanatory.

If you have multiple languages you will be asked which language should be used.

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

   Module Task Queue with created tasks


Send a test before Newsletter sending
-------------------------------------

There are two Buttons to send a Test-Newsletter. They are using the same Test-Recipients-List.
The Button "Test (attach images)" is just for developers or people they want test
the layout (html) in Mail Clients. It's **not** purposed for sending many mails with
embedded images.

.. figure:: /Images/Build/NewsletterModuleButtons.png
   :class: with-shadow
   :alt: Cutemailing Buttons

   Cute Mailing Module Buttons in list


Multilanguge Newsletter
-----------------------

Cute Mailing support multilange Newsletter from version 2.1.
To create multilanguage newsletters configure your languages in your
:ref:`Site Config <t3coreapi:sitehandling-basics>`.

On creating a new Newsletter, you should see now an additionally step before the wizard.
Here you can choose the page (uid) which should be send and you can choose
the language.

.. figure:: /Images/Build/CreateMultilanguageNewsletter.png
   :class: with-shadow
   :alt: Module Cute Mailing: Creating multilanguage Newsletter

   Module Cute Mailing: Additionally step on creating multilanguage Newsletter

.. important::

   Be sure the selected page is translated! Otherwise they are errors in wizard preview
   on accessing the page.

If you dont want to use multilanguage support (just default language) you can
disable this feature and the additionally step in the wizard by setting `mod.web_modules.cute_mailing.hideLanguage` to `1`.

Set `mod.web_modules.cute_mailing.language` to the default language you want to use, this can be overwritten
by language selection on the first step.

See :ref:`Configuration <configuration_example>` section.

.. important::

    If you start sending newsletter, it can be take a while until the first mail
    was really sent! Let's assume Scheduler Task will be executed every 5 minutes.

    - you click on send Button, 5 minutes pass
    - newsletter task will be executed, mail tasks are created, 5 minutes pass
    - mail tasks will be executed, first mails are sent

    So it can take up to 10 minutes until the first mail was send!
