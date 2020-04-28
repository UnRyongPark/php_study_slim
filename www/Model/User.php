<?php

namespace Model;

require_once '../Util/DbConn.php';
require_once '../DataModel/User.php';

use DataModel\User as UserModel;
use stdClass;
use Util\DbConn;

class User
{
    private $dbConn;

    public function __construct()
    {
        $this->dbConn = new DbConn();
    }

    /**
     * @return UserModel[]
     */
    public function findAllUser(): array
    {
        $readCon = $this->dbConn->getReadCon();
        $list = [];
        if ($result = $readCon->query('select * from wrongStudy.users;')) {
            while ($obj = $result->fetch_object(UserModel::class)) {
                $list[] = $obj;
            }
        }

        return $list;
    }

    /**
     * @param string $k
     * @param int $offset
     * @param int $count
     * @return UserModel[]
     */
    public function findUsersByName(string $k, int $offset = 0, int $count = 15): array
    {
        $q = $this->dbConn->getReadCon()->prepare('select * from wrongStudy.users where name = ? limit ?, ?;');
        $q->bind_param('sii', $k, $offset, $count);
        $q->execute();

        $list = [];

        if ($result = $q->get_result()) {
            while ($obj = $result->fetch_object(UserModel::class)) {
                $list[] = $obj;
            }
        }

        return $list;
    }

    /**
     * @param string $k
     * @param int $offset
     * @param int $count
     * @return UserModel[]
     */
    public function findUsersByNameStartParts(string $k, int $offset = 0, int $count = 15): array
    {
        $q = $this->dbConn->getReadCon()->prepare('select * from wrongStudy.users where `name` like ? limit ?, ?;');
        $q->bind_param('sii', $k, $offset, $count);
        $q->execute();

        $list = [];

        if ($result = $q->get_result()) {
            while ($obj = $result->fetch_object(UserModel::class)) {
                $list[] = $obj;
            }
        }

        return $list;
    }

    /**
     * @return UserModel[]
     */
    public function findUsersByEmailStartParts($k, $offset = 0, $count = 15): array
    {
        $q = $this->dbConn->getReadCon()->prepare('select * from wrongStudy.users where email Like ? limit ?, ?;');
        $q->bind_param('sii', $k, $offset, $count);
        $q->execute();

        $list = [];

        if ($result = $q->get_result()) {
            while ($obj = $result->fetch_object(UserModel::class)) {
                $list[] = $obj;
            }
        }

        return $list;
    }

    /**
     * @param string $id
     * @return bool|UserModel|object|stdClass
     */
    public function findUserById(string $id)
    {
        $q = $this->dbConn->getReadCon()->prepare('select * from wrongStudy.users where id = ?;');
        $q->bind_param('s', $id);
        $q->execute();

        if ($result = $q->get_result()) {
            return $result->fetch_object(UserModel::class);
        }

        return false;
    }

    /**
     * @param string $email
     * @return bool|UserModel|object|stdClass
     */
    public function findUserByEmail(string $email)
    {
        $q = $this->dbConn->getReadCon()->prepare('select * from wrongStudy.users where email = ?;');
        $q->bind_param('s', $email);
        $q->execute();

        if ($result = $q->get_result()) {
            return $result->fetch_object(UserModel::class);
        }

        return false;
    }

    /**
     * @param UserModel $u
     * @return string
     */
    public function addUser(UserModel $u): string
    {
        $con = $this->dbConn->getWriteCon();
        if ($u->getGender() === null) {
            $q = $con->prepare('insert into wrongStudy.users (name, nickname, password, cellphone, email, signup_date) values (?, ?, ?, ?, ?, now());');
            $q->bind_param('sssss', $u->getName(), $u->getNickname(),
                password_hash($u->getPassword(), PASSWORD_DEFAULT), $u->getCellphone(), $u->getEmail());
        } else {
            $q = $con->prepare('insert into wrongStudy.users (name, nickname, password, cellphone, email, gender, signup_date) values (?, ?, ?, ?, ?, ?, now());');
            $q->bind_param('ssssss', $u->getName(), $u->getNickname(),
                password_hash($u->getPassword(), PASSWORD_DEFAULT), $u->getCellphone(), $u->getEmail(),
                $u->getGender());
        }

        if ($q->execute()) {
            return $con->insert_id;
        }

        return $con->error;
    }
}