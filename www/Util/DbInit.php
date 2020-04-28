<?php

namespace Util;

class DbInit
{
    /**
     * @return string
     */
    public function init(): string
    {
        $con = new \mysqli('wrongtips_mysql', 'root', 'wrongtips');

        if ($con->connect_errno) {
            throw new \RuntimeException($con->connect_error);
        }

        $result = '';

        if ($con->query('create schema wrongStudy DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;') === true) {
            $result .= "Schema successfully created.\n";
        }

        if ($con->query('use wrongStudy;') === true) {
            $result .= "Schema successfully changed.\n";
        }

        if ($con->query('create table users
                                    (
                                        id bigint auto_increment,
                                        name varchar(20) not null,
                                        nickname varchar(30) not null,
                                        password varchar(255) not null,
                                        cellphone varchar(20) not null,
                                        email varchar(100) not null,
                                        gender char null,
                                        signup_date timestamp not null,
                                        constraint users_pk
                                            primary key (id)
                                    )
                                    ENGINE = InnoDB
                                    DEFAULT CHARACTER SET = utf8mb4 COLLATE utf8mb4_general_ci;') === true) {
            $result .= "Table successfully created.\n";
        }

        if ($con->query('create unique index users_email_uindex on users (email);') === true) {
            $result .= "Email Unique index successfully created.\n";
        }

        if ($con->query('create unique index users_nickname_uindex on users (nickname);') === true) {
            $result .= "Nickname Unique index successfully created.\n";
        }

        return $result === "Schema successfully changed.\n" ? 'already init' : $result;
    }
}