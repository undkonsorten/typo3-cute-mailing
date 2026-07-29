#
# Table structure for table 'tx_cutemailing_domain_model_newsletter'
#
CREATE TABLE tx_cutemailing_domain_model_newsletter
(
	newsletter_page        int(11) DEFAULT '0' NOT NULL,
	send_outs              int(11) DEFAULT '0' NOT NULL,
	recipient_list         int(11) DEFAULT '0' NOT NULL,
	test_recipient_list    int(11) DEFAULT '0' NOT NULL,
	language               tinyint(4) DEFAULT '0' NOT NULL,
	listunsubscribe_enable tinyint(4) DEFAULT '0' NOT NULL,
	listunsubscribe_email  varchar(255) DEFAULT '' NOT NULL
);

#
# Table structure for table 'tx_cutemailing_domain_model_recipientlist'
#
CREATE TABLE tx_cutemailing_domain_model_recipientlist
(
	recipient_list_page int(11) DEFAULT '0' NOT NULL
);

CREATE TABLE tx_taskqueue_domain_model_task
(
	tx_cutemailing_newsletter int(11) UNSIGNED DEFAULT 0 NOT NULL,
	tx_cutemailing_sendout    int(11) UNSIGNED DEFAULT 0 NOT NULL
);
