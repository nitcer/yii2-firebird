<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace edgardmessias\db\firebird;

use \PDO;

/**
 * Description of PdoAdapter
 *
 * @author Edgard Lorraine Messias <edgard.cpd@romera.com.br>
 */
class PdoAdapter extends PDO
{
    /**
     * Do some basic setup for Firebird.
     * o Force use of exceptions on error.
     * o Force all metadata to lower case.
     *   Yii will behave in unpredicatable ways if
     *   metadata is not lowercase.
     * o Ensure that table names are not prefixed to
     *    fieldnames when returning metadata.
     * Finally call parent constructor.
     *
     */

    public function __construct($dsn, $username, $password, $driver_options = [])
    {
        // Windows OS paths with backslashes should be changed
        $dsn = str_replace('\\', '/', $dsn);
        // apply error mode
        $driver_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
        // lower case column names in results are necessary for Yii ActiveRecord proper functioning
        $driver_options[PDO::ATTR_CASE] = PDO::CASE_LOWER;
        // ensure we only receive fieldname not tablename.fieldname.
        $driver_options[PDO::ATTR_FETCH_TABLE_NAMES] = false;
        parent::__construct($dsn, $username, $password, $driver_options);
    }

    public function beginTransaction($isolationLevel = null): bool
    {
        $level = match ($isolationLevel ?? \yii\db\Transaction::READ_COMMITTED) {
            \yii\db\Transaction::READ_COMMITTED => \Pdo\Firebird::READ_COMMITTED,
            \yii\db\Transaction::READ_UNCOMMITTED => \Pdo\Firebird::READ_COMMITTED,
            \yii\db\Transaction::REPEATABLE_READ => \Pdo\Firebird::REPEATABLE_READ,
            \yii\db\Transaction::SERIALIZABLE => \Pdo\Firebird::SERIALIZABLE,
            default => throw new \InvalidArgumentException("Unsupported isolation level: $isolationLevel"),
        };

        $this->setAttribute(\Pdo\Firebird::TRANSACTION_ISOLATION_LEVEL, $level);
        return parent::beginTransaction();
    }

    public function commit(): bool
    {
        return parent::commit();
    }

    public function rollBack(): bool
    {
        return parent::rollBack();
    }

    public function inTransaction(): bool
    {
        return parent::inTransaction();
    }
}
