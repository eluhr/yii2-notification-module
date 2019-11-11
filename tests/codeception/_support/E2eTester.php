<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
 */
class E2eTester extends \Codeception\Actor
{
    use _generated\E2eTesterActions;

    /**
     * Login with username and password
     *
     * @param $username
     * @param $password
     *
     * @throws Exception
     */
    public function login($username, $password)
    {
        $this->amOnPage('/user/security/login');
        $this->fillField('input[name="LoginForm[login]"]', $username);
        $this->fillField('input[name="LoginForm[password]"]', $password);
        $this->click('#LoginForm button');
        $this->waitForElementNotVisible('#LoginForm', 10);
    }

    /**
     * Credit: https://stackoverflow.com/questions/29168107/how-to-fill-a-rich-text-editor-field-for-a-codeception-acceptance-test#answer-33480861
     *
     * @param $elementId
     * @param $content
     */
    public function fillCkEditorById($elementId, $content)
    {
        $selector = \Facebook\WebDriver\WebDriverBy::cssSelector('#cke_' . $elementId . ' .cke_wysiwyg_frame');
        $this->executeInSelenium(
            function (\Facebook\WebDriver\Remote\RemoteWebDriver $web_driver)
            use ($selector, $content) {
                $web_driver->switchTo()->frame(
                    $web_driver->findElement($selector)
                );

                $web_driver->executeScript(
                    'arguments[0].innerHTML = "' . addslashes($content) . '"',
                    [$web_driver->findElement(\Facebook\WebDriver\WebDriverBy::tagName('body'))]
                );

                $web_driver->switchTo()->defaultContent();
            });
    }


    /**
     * @param $selector
     * @param array $values
     */
    public function select2Option($selector, $values = [])
    {
        $options = implode(',', (array)$values);
        $this->executeJS(<<<JS
 $('{$selector}').select2("val", [{$options}]);
JS
        );
    }

}
