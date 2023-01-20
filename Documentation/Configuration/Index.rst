.. include:: /Includes.rst.txt

.. _configuration:

=============
Configuration
=============

There is no TypoScript configuration needed. You can use the usual settings
to override template paths.

You need at least one Newsletter Sysfolder with Doktype "Cute Mailing".

.. figure:: /Images/Build/CuteMailingSysfolder.png
   :class: with-shadow
   :alt: Created Sysfolder with doktype "Cute Mailing"

   Create Sysfolder with doktype "Cute Mailing"

Page TS-Config
--------------

You can configure your newsletter default via Page TSconfig.

If you dont need multi language support you can deactivate it via:

.. code-block:: typoscript

   mod.web_modules.cute_mailing.hideLanguageSelection = 1

The language of created newsletter can also be set via page TSconfig:

.. code-block:: typoscript

   mod.web_modules.cute_mailing.language = 1

Typical example
~~~~~~~~~~~~~~~

.. code-block:: typoscript

   mod.web_modules.cute_mailing {
      sender = newsletter@undkonsorten.com
      sender_name = Undkonsorten
      reply_to = newsletter@undkonsorten.com
      reply_to_name = Undkonsorten
      # we use a page type to render html code optimized for emails
      page_type_html = 101
      # and a page type to render text emails
      page_type_text = 111
      # list markers allowed to be replaced in the rendered page
      allowed_marker = firstName,lastName,registeraddresshash
      return_path = bounce@undkonsorten.com
   }

Taskqueue
---------

The `taskqueue` extension must be configured to run regularly, best via Scheduler.

.. _configuration_scheduler_task:

Create a new Scheduler Task
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Choose "Execute Console Commands" in the field "Class" in Scheduler Task.
The type should be "recurring". Set the frequency you need.
After saving the new task you can select the "taskqueue:run-tasks" as the "Schedulable Command".
Set the limit for "how many task should be executed in one run" and save the task.

Now you are prepared for sending Newsletters. Scheduler will start executing the
spooled Tasks in the "Task Queue".
