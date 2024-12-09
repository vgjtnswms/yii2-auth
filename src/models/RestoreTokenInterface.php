<?php

namespace vs\yii2\auth\models;

/**
 * Undocumented interface
 */
interface RestoreTokenInterface
{
    /**
     * @return UserInterface
     */
    public function getUser();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $email
     * @return void
     */
    public function setEmail($email);

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param string $token
     * @return void
     */
    public function setToken($token);

    /**
     * @return string
     */
    public function getInfo();

    /**
     * @param array $info
     * @return void
     */
    public function setInfo($info);
}
