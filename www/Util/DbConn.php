<?php

namespace Util;

/**
 * Class DbConn
 * @package Util
 */
class DbConn
{
    /**
     * @var \mysqli
     */
    private $readCon;

    /**
     * @var \mysqli
     */
    private $writeCon;

    /**
     * @return \mysqli
     */
    public function getReadCon(): \mysqli
    {
        if ($this->readCon !== null) {
            return $this->readCon;
        }

        $con = new \mysqli('wrongtips_mysql', 'root', 'wrongtips');

        if ($con->connect_error) {
            throw new \RuntimeException($con->connect_error);
        }

        $this->readCon = $con;

        return $this->readCon;
    }

    /**
     * @return \mysqli
     */
    public function getWriteCon(): \mysqli
    {
        if ($this->writeCon !== null) {
            return $this->writeCon;
        }

        $con = new \mysqli('wrongtips_mysql', 'root', 'wrongtips');

        if ($con->connect_error) {
            throw new \RuntimeException($con->connect_error);
        }

        $this->writeCon = $con;

        return $this->writeCon;
    }

    public function __destruct()
    {
        if ($this->readCon !== null) {
            $this->readCon->close();
            $this->readCon = null;
        }

        if ($this->writeCon !== null) {
            $this->writeCon->close();
            $this->writeCon = null;
        }
    }
}