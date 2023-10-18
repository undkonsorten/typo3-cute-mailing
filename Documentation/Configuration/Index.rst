.. include:: /Includes.rst.txt

.. _configuration:

=============
Configuration
=============

There is no TypoScript configuration needed. You can use the usual settings
to override template paths.

.. _configuration_setup_sysfolder:

Set up CuteMailing Sysfolder
----------------------------

You need at least one Newsletter Sysfolder with module "Cute Mailing". Set field
"Contains Plugin" in div "Behaviour" in page properties.

.. figure:: /Images/Build/CuteMailingSysfolder.png
   :class: with-shadow
   :alt: Created Sysfolder with module "Cute Mailing"

   Create Sysfolder with module "Cute Mailing"

Page TS-Config
--------------

You can configure your newsletter default via Page TSconfig.

.. _configuration_example:

Typical example
~~~~~~~~~~~~~~~

.. code-block:: typoscript

   mod.web_modules.cute_mailing {
      # default sender mail
      sender = newsletter@undkonsorten.com
      # default sender name
      sender_name = Undkonsorten
      # default reply to mail
      reply_to = newsletter@undkonsorten.com
      # default reply to name
      reply_to_name = Undkonsorten
      # we use a page type to render html code optimized for emails
      page_type_html = 10
      # and a page type to render text emails
      page_type_text = 11
      # list markers allowed to be replaced in the rendered page
      allowed_marker = firstName,lastName,registeraddresshash
      # return path, e.g. for bounce mails
      return_path = bounce@undkonsorten.com
      # If you dont need multi language support you can deactivate it
      hideLanguageSelection = 1
      # uid of the default language of created newsletter
      language = 1
      # Basic auth if your newsletter page needs auth
      # Basic auth user
      basic_auth_user =
      #Basic auth password
      basic_auth_password =
      listunsubscribe_enable = 1
      listunsubscribe_email = unsubscribe@example.com
}
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
Set the limit for "how many task should be executed in one run" (equal to how many mails will be sent in one run)
and save the task.

Now you are prepared for sending Newsletters. Scheduler will start executing the
spooled Tasks in the "Task Queue".
