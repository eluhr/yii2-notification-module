<?php

use igorw\FailingTooHardException;

/**
 * @author Elias Luhr <elias.luhr@gmail.com>
 */
class MysqlController extends \dmstr\console\controllers\MysqlController
{
    /**
     * @param null $dsn
     * @param null $user
     * @param null $password
     *
     * @throws \yii\base\ExitException
     */
    public function actionWaitForConnection(
        $dsn = null,
        $user = null,
        $password = null
    ) {
        $dsn = $dsn ?: getenv('DATABASE_DSN_BASE');
        $user = $user ?: getenv('DB_ENV_MYSQL_ROOT_USER');
        $password = $password ?: getenv('DB_ENV_MYSQL_ROOT_PASSWORD');

        if (empty($user) || empty($password) || empty($dsn)) {
            $this->stderr('Configuration failed, aborting.');
            return;
        }

        // trying to connect to database with PDO (20 times, interval 1 second)
        $this->stdout(
            "Checking database connection on DSN '{$dsn}' with user '{$user}'"
        );

        try {
            // retry an operation up to 20 times
            $pdo = $this->retry(
                $this->mysqlRetryMaxCount,
                function () use ($dsn, $user, $password) {
                    $this->stdout('.');
                    sleep($this->mysqlRetryTimeout);
                    return new \PDO($dsn, $user, $password);
                }
            );
        } catch (FailingTooHardException $e) {
            $this->stderr("\n\nError: Unable to connect to database '".$e->getMessage()."''");
            \Yii::$app->end(1);
        }
        $this->stdout(' [OK]'.PHP_EOL);

    }

    /**
     * @param $retries
     * @param callable $fn
     *
     * @return mixed
     * @throws FailingTooHardException
     */
    private function retry($retries, callable $fn)
    {
        beginning:
        try {
            return $fn();
        } catch (\Exception $e) {
            if (!$retries) {
                throw new FailingTooHardException('', 0, $e);
            }
            $retries--;
            goto beginning;
        }
    }
}