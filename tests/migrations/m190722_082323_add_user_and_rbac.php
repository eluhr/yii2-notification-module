<?php

use yii\db\Migration;

class m190722_082323_add_user_and_rbac extends Migration
{
    /**
     * @return bool|void
     */
    public function safeUp()
    {
        // user-1 : user-password-1 usw.
        $this->execute(<<<SQL
INSERT INTO `user` (`id`, `username`, `email`, `password_hash`, `auth_key`, `unconfirmed_email`, `registration_ip`, `flags`, `confirmed_at`, `blocked_at`, `updated_at`, `created_at`, `last_login_at`, `last_login_ip`, `auth_tf_key`, `auth_tf_enabled`, `password_changed_at`, `gdpr_consent`, `gdpr_consent_date`, `gdpr_deleted`)
VALUES
	(1, 'user-1', 'user-1@local.develop', '$2y$10\$I4eMVzQwsDB/md1dkrV7t.c5LKsB5E.rwf6z9rTZi4CUa6FCdXTxC', 'sQYRiLko_jlgXOV2vL7rC1za4m51nmp5', NULL, NULL, 0, 1563783902, NULL, 1563783902, 1563783902, NULL, NULL, '', 0, 1563783902, 0, NULL, 0),
	(2, 'user-2', 'user-2@local.develop', '$2y$10\$nSSjd64.eIFmwclEbqgJNeh8r8JwR1njRFPpbkbbMFipVR2iFNlSW', 'sQYRiLko_jlgXOV2vL7rC1za4m51nmp5', NULL, NULL, 0, 1563783902, NULL, 1563783902, 1563783902, NULL, NULL, '', 0, 1563783902, 0, NULL, 0),
	(3, 'user-3', 'user-3@local.develop', '$2y$10\$Y0UmdiUniJUms2nko6Bfqu7FiXCfbfuXXSWGpgCEpYMArom4D0VpC', 'sQYRiLko_jlgXOV2vL7rC1za4m51nmp5', NULL, NULL, 0, 1563783902, NULL, 1563783902, 1563783902, NULL, NULL, '', 0, 1563783902, 0, NULL, 0),
	(4, 'user-4', 'user-4@local.develop', '$2y$10\$zxT4okFJRVu4jR2kYjmRAOk6YC6RqcEB4z8SSolAfYpB4KT0tX/O2', 'sQYRiLko_jlgXOV2vL7rC1za4m51nmp5', NULL, NULL, 0, 1563783902, NULL, 1563783902, 1563783902, NULL, NULL, '', 0, 1563783902, 0, NULL, 0),
	(5, 'user-5', 'user-5@local.develop', '$2y$10\$RDjTH39BtA4gC8CBBRFbb.8nV5FUJqjGz5MTa/xv8lHGgaHQVdUyq', 'sQYRiLko_jlgXOV2vL7rC1za4m51nmp5', NULL, NULL, 0, 1563783902, NULL, 1563783902, 1563783902, NULL, NULL, '', 0, 1563783902, 0, NULL, 0);

INSERT INTO `profile` (`user_id`, `name`, `public_email`, `gravatar_email`, `gravatar_id`, `location`, `website`, `timezone`, `bio`)
VALUES
	(1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`)
VALUES
	('notification.compose_a_message', '1', NULL),
	('notification.compose_a_message', '2', NULL),
	('notification.compose_a_message', '3', NULL),
	('notification.send_mail_to_everyone', '1', NULL),
	('notification.user_group', '2', NULL),
	('notification.send_priority_mail', '3', NULL),
	('NotificationAdmin', '4', NULL);
SQL
);
    }

    /**
     * @return bool
     */
    public function safeDown()
    {
        echo "m190722_082323_add_user_and_rbac cannot be reverted.\n";
        return false;
    }
}
