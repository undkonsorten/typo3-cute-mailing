#
# Table structure for table 'tx_cutemailing_domain_model_newsletter'
#
CREATE TABLE tx_cutemailing_domain_model_newsletter
(

	uid                 int(11)                         NOT NULL auto_increment,
	pid                 int(11)             DEFAULT '0' NOT NULL,

	newsletter_page     int(11)             DEFAULT '0' NOT NULL,
	send_outs				    int(11)             DEFAULT '0' NOT NULL,
	recipient_list      int(11)             DEFAULT '0' NOT NULL,
	test_recipient_list int(11)             DEFAULT '0' NOT NULL,
	sender              varchar(255)        DEFAULT ''  NOT NULL,
	sender_name         varchar(255)        DEFAULT ''  NOT NULL,
	reply_to            varchar(255)        DEFAULT ''  NOT NULL,
	reply_to_name       varchar(255)        DEFAULT ''  NOT NULL,
	return_path         varchar(255)        DEFAULT ''  NOT NULL,
	language            tinyint(4)          DEFAULT '0' NOT NULL,
	title               varchar(255)        DEFAULT ''  NOT NULL,
	subject             varchar(255)        DEFAULT ''  NOT NULL,
	description         varchar(255)        DEFAULT ''  NOT NULL,
	status              int(11)             DEFAULT '0' NOT NULL,
	sending_time        int(11) unsigned    DEFAULT '0' NOT NULL,
	page_type_html      int(11)             DEFAULT '0' NOT NULL,
	page_type_text      int(11)             DEFAULT '0' NOT NULL,
	allowed_marker      varchar(255)        DEFAULT ''  NOT NULL,
	basic_auth_password  varchar(255)        DEFAULT ''  NOT NULL,
	basic_auth_user      varchar(255)        DEFAULT ''  NOT NULL,

	tstamp              int(11) unsigned    DEFAULT '0' NOT NULL,
	crdate              int(11) unsigned    DEFAULT '0' NOT NULL,
	cruser_id           int(11) unsigned    DEFAULT '0' NOT NULL,
	deleted             tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden              tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime           int(11) unsigned    DEFAULT '0' NOT NULL,
	endtime             int(11) unsigned    DEFAULT '0' NOT NULL,
	type                varchar(255)        DEFAULT ''  NOT NULL,

	t3ver_oid           int(11)             DEFAULT '0' NOT NULL,
	t3ver_id            int(11)             DEFAULT '0' NOT NULL,
	t3ver_wsid          int(11)             DEFAULT '0' NOT NULL,
	t3ver_label         varchar(255)        DEFAULT ''  NOT NULL,
	t3ver_state         tinyint(4)          DEFAULT '0' NOT NULL,
	t3ver_stage         int(11)             DEFAULT '0' NOT NULL,
	t3ver_count         int(11)             DEFAULT '0' NOT NULL,
	t3ver_tstamp        int(11)             DEFAULT '0' NOT NULL,
	t3ver_move_id       int(11)             DEFAULT '0' NOT NULL,

	sys_language_uid    int(11)             DEFAULT '0' NOT NULL,
	l10n_parent         int(11)             DEFAULT '0' NOT NULL,
	l10n_diffsource     mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid, t3ver_wsid),
	KEY language (l10n_parent, sys_language_uid)

);

CREATE TABLE tx_cutemailing_domain_model_sendout
(
    test       tinyint(4) DEFAULT '0' NOT NULL,
    newsletter int(11)    DEFAULT '0' NOT NULL,
    mail_tasks int(11)    DEFAULT '0' NOT NULL,
    total      int(11)    DEFAULT '0',
    completed  int(11)    DEFAULT '0'
);



#
# Table structure for table 'tx_cutemailing_domain_model_recipient_list'
#
CREATE TABLE tx_cutemailing_domain_model_recipientlist
(

	uid                 int(11)                         NOT NULL auto_increment,
	pid                 int(11)             DEFAULT '0' NOT NULL,

	name                varchar(255)        DEFAULT ''  NOT NULL,
	recipient_list_page int(11)             DEFAULT '0' NOT NULL,
	record_type         varchar(255)        DEFAULT ''  NOT NULL,
	line_separated_list text,

	tstamp              int(11) unsigned    DEFAULT '0' NOT NULL,
	crdate              int(11) unsigned    DEFAULT '0' NOT NULL,
	cruser_id           int(11) unsigned    DEFAULT '0' NOT NULL,
	deleted             tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden              tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime           int(11) unsigned    DEFAULT '0' NOT NULL,
	endtime             int(11) unsigned    DEFAULT '0' NOT NULL,
	type                varchar(255)        DEFAULT ''  NOT NULL,

	t3ver_oid           int(11)             DEFAULT '0' NOT NULL,
	t3ver_id            int(11)             DEFAULT '0' NOT NULL,
	t3ver_wsid          int(11)             DEFAULT '0' NOT NULL,
	t3ver_label         varchar(255)        DEFAULT ''  NOT NULL,
	t3ver_state         tinyint(4)          DEFAULT '0' NOT NULL,
	t3ver_stage         int(11)             DEFAULT '0' NOT NULL,
	t3ver_count         int(11)             DEFAULT '0' NOT NULL,
	t3ver_tstamp        int(11)             DEFAULT '0' NOT NULL,
	t3ver_move_id       int(11)             DEFAULT '0' NOT NULL,

	sys_language_uid    int(11)             DEFAULT '0' NOT NULL,
	l10n_parent         int(11)             DEFAULT '0' NOT NULL,
	l10n_diffsource     mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid, t3ver_wsid),
	KEY language (l10n_parent, sys_language_uid)

);

CREATE TABLE tx_taskqueue_domain_model_task(
  tx_cutemailing_newsletter	int(11) DEFAULT '0' NOT NULL,
  tx_cutemailing_sendout	int(11) DEFAULT '0' NOT NULL
);
