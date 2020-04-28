<?php

namespace Application;

require_once '../Model/User.php';
require_once '../DataModel/User.php';


use Firebase\JWT\JWT;
use Model\User as UserModel;
use DataModel\User as UserDataModel;


class User
{
    private $model = null;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    /**
     * @return UserModel[]
     */
    public function findAllUser(): array
    {
        $users = $this->model->findAllUser();

        $list = [];

        foreach ($users as $user) {
            $list[] = $user->getOutputData();
        }


        return $list;
    }

    /**
     * @param array $jsonArr
     * @return bool|string
     */
    public function signInUser(array $jsonArr)
    {
        $j = (new UserDataModel())->fromArray($jsonArr);

        if ($j->signInValidation() !== false) {
            $targetUser = $this->model->findUserByEmail($j->getEmail());

            if ($targetUser) {
                if (password_verify($j->getPassword(), $targetUser->getPassword())) {
                    $payload = $targetUser->getOutputData();
                    $payload['iss'] = 'http://study.wrong.tips';
                    $payload['aud'] = 'http://study.wrong.tips';
                    $payload['iat'] = time();
                    $payload['nbf'] = time();

                    $payload['exp'] = time() + (3 * 60 * 60);

                    return JWT::encode($payload, SAMPLE_JWT_KEY);
                } else {
                    throw new \RuntimeException('Not match password');
                }
            }

            throw new \RuntimeException('Not found user');
        }

        return false;
    }

    /**
     * @param string $id
     * @return bool|UserDataModel|object|\stdClass
     */
    public function getUser(string $id)
    {
        if ($user = $this->model->findUserById($id)) {
            return $user;
        }

        throw new \RuntimeException('not found user');
    }

    /**
     * @param array $jsonArr
     * @return bool|string
     */
    public function addUser(array $jsonArr)
    {
        $j = (new UserDataModel())->fromArray($jsonArr);

        if ($j->insertValidation() !== false) {
            $add = $this->model->addUser($j);
            if (is_numeric($add)) {
                return $add;
            } else {
                throw new \RuntimeException($add);
            }
        }

        return false;
    }
}