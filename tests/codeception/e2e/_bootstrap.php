<?php

use Codeception\Util\Fixtures;

Fixtures::add('restricted-user',[
    'username' => 'user-5',
    'password' => 'user-password-5'
]);

Fixtures::add('user-group-user',[
    'username' => 'user-2',
    'password' => 'user-password-2'
]);

Fixtures::add('compose-mail-user',[
    'username' => 'user-1',
    'password' => 'user-password-1'
]);
Fixtures::add('receiver-user', Fixtures::get('restricted-user'));