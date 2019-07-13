<?php


namespace eluhr\notification\components\helpers;


/**
 * @package eluhr\notification\components\helpers
 * @author Elias Luhr <elias.luhr@gmail.com>
 */
class Permission
{
    const SEND_MESSAGE_TO_EVERYONE = 'notification.send_mail_to_everyone';
    const USER_GROUPS = 'notification.user_group';
    const COMPOSE_A_MESSAGE = 'notification.compose_a_message';
    const SEND_PRIORITY_MESSAGE = 'notification.send_priority_mail';
}