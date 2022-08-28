<?php
/**
 * @pakages    :easy_api
 * @auther     :https://github.com/alnazer
 * @created_at :26,August 2022 2:31 AM
 * @filename   :UserInterface.php
 **/

/*
|--------------------------------------------------------------------------
| Class title
|--------------------------------------------------------------------------
|
| Description Here
|
*/

namespace Alnazer\Easyapi\database;

interface UserInterface
{
    /**
     * @return mixed
     */

    public static function findIdentityByAccessToken($token = null);

    public static function findIdentityByUsernamePassword($username, $password);

    public static function hashPassword($password);

    public static function verifyPassword($enter_password, $currency_password);

    public static function login($username, $password);

    public static function usernameFiled();

}