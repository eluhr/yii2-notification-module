<?php

use Codeception\Util\Fixtures;

/**
 * @author Elias Luhr <elias.luhr@gmail.com>
 */
class NotificationCest
{

    /**
     * @param string $user_identifier
     * @param E2eTester $I
     *
     * @throws Exception
     */
    private function userLogin($user_identifier, $I)
    {
        $user = Fixtures::get($user_identifier);
        $I->login($user['username'], $user['password']);
        $I->amOnPage('/notification');
    }

    /**
     * @param E2eTester $I
     *
     * @throws Exception
     */
    public function checkRestrictedPermissions(E2eTester $I)
    {
        $I->wantTo("make sure that a user with restricted privileges is not able to do something he shouldn't be able to do");

        $this->userLogin('restricted-user', $I);

        $I->wantToTest('if user is able to compose a mail');

        $I->cantSee('Compose', 'a.btn');
        $I->cantSee('User Groups', 'a.btn');
        $I->amOnPage('/notification/inbox/compose');
        $I->see('Forbidden');
        $I->amOnPage('/notification/inbox/user-group');
        $I->see('Forbidden');
        $I->amOnPage('/notification/inbox/user-group-edit');
        $I->see('Forbidden');
    }

    /**
     * @param E2eTester $I
     *
     * @throws Exception
     */
    public function checkComposeMailPermissions(E2eTester $I)
    {
        $I->wantTo('make sure that a user with compose mail permissions is able to send notifications to users');

        $this->userLogin('compose-mail-user', $I);

        $I->wantToTest('if user is able to send a notification');

        $I->see('Compose', 'a.btn');

        $I->click('Compose');
        $I->waitForElementVisible('form');

        $I->click('Send');

        $I->waitForElementVisible('.field-message-receiver_ids.has-error');
        $I->see('Receivers cannot be blank.');
        $I->waitForElementVisible('.field-message-subject.has-error');
        $I->see('Subject cannot be blank.');
        $I->waitForElementVisible('.field-message-text.has-error');
        $I->see('Message cannot be blank.');

        $I->select2Option('#message-receiver_ids',1);
        $I->fillField('input[name="Message[subject]"]','A important message');
        $I->fillCkEditorById('#message-text','Hello User 1!');

        $I->makeScreenshot('filled-form');
        $I->click('Send');

        $I->waitForElementVisible('table');
    }

    /**
     * @param E2eTester $I
     *
     * @throws Exception
     */
    public function checkUserGroupPermissions(E2eTester $I)
    {
        $I->wantTo('make sure that a user with user group permissions is able to manage user groups');

        $this->userLogin('user-group-user', $I);

        $I->wantToTest('if user is able to create a user group');

        $I->see('Compose', 'a.btn');
        $I->see('User Groups', 'a');
        $I->amOnPage('/notification/inbox/user-group');
        $I->see('There are no user groups yet.', '.empty');
        $I->click('Add new user group');
        $I->waitForElementVisible('form');

        $I->click('Save');

        $I->waitForElementVisible('.field-messageusergroup-name.has-error');
        $I->see('Name cannot be blank.');
        $I->waitForElementVisible('.field-messageusergroup-receiver_ids.has-error');
        $I->see('Receivers cannot be blank.');

        $I->fillField('input[name="MessageUserGroup[name]"]', 'My custom user group');

        $I->select2Option('#messageusergroup-receiver_ids',1);

        $I->click('Save');

        $I->waitForElementVisible('table');

        $I->see('My custom user group');

        $I->wantToTest('if user is able to select a user group');

        $I->amOnPage('/notification/inbox/compose');

        $I->click('.select2-selection');

        $I->makeScreenshot('Open');

        $I->see('User Groups','.select2-results__group');
        $I->see('My custom user group');

        $I->wantToTest('if user is able to delete a user group');

        $I->amOnPage('/notification/inbox/user-group');

        $I->click('td button.dropdown-toggle');
        $I->waitForElementVisible('.dropdown-menu');
        $I->see('Delete');
        $I->click('Delete');

        $I->waitForText('There are no user groups yet.');
    }
}
