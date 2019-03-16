<?php

 /**
  * This file contains configuration for the application.
  * It will be used by app/core/Config.php
  *
  * @license    http://opensource.org/licenses/MIT The MIT License (MIT)
  * @author     Omar El Gabry <omar.elgabry.93@gmail.com>
  */

return array(

    /**
     * Configuration for: Database Connection
     * Define database constants to establish a connection.
     *
     * It's important to set the charset for the database
     *
     * In the real world, you will have a database user with limited privileges(usually only CRUD).
     *
     */
    "URL" => "http://" . $_SERVER['HTTP_HOST'] . str_replace('public', '', dirname($_SERVER['SCRIPT_NAME'])),
    /**
     * Configuration for: Folders
     * Usually there's no reason to change this.
     */
    "PATH_CONTROLLER" => realpath(dirname(__FILE__).'/../../') . "/app/controller/",
    "PATH_VIEW" => realpath(dirname(__FILE__).'/../../') . "/app/view/",

    "DB_HOST" =>"your hostName",
    "DB_NAME" => "your dbName",
    "DB_USER" => "root",
    "DB_PASS" => "",
    'DB_CHARSET' => 'utf8',

    //"LANG" => ["name" => "Italian", "code" => "it", "locale" => "it_IT.UTF-8,it_IT,italian", "image" => "it"],
    "LANG" => "it",
    //"LANGUAGES" => ["it","en","fr"],
    "LANGUAGES" => [ "it" => ["name" => "Italiano", "code" => "it", "locale" => "it_IT.UTF-8,it_IT,italian", "image" => "it"], "en" => ["name" => "English", "code" => "en", "locale" => "en_US.UTF-8,en_US,en-gb,english", "image" => "gb"], "fr" => ["name" => "French", "code" => "fr", "locale" => "fr_CA.UTF-8,fr_FR,fr-fr,,french", "image" => "fr"]],

    /**
     * Configuration for: Paths
     * Paths from views directory
     */
    "VIEWS_PATH"  => APP . "templates/",
    "ERRORS_PATH" => APP . "templates/errors/",
    "LOGIN_PATH" => APP . "templates/login/",
    "ADMIN_VIEWS_PATH" => APP . "templates/back/",

    /**
     * Configuration for: Cookies
     *
     * COOKIE_RUNTIME: How long should a cookie be valid by seconds.
     *      - 1209600 means 2 weeks
     *      - 604800 means 1 week
     * COOKIE_DOMAIN: The domain where the cookie is valid for.
     *      COOKIE_DOMAIN mightn't work with "localhost", ".localhost", "127.0.0.1", or ".127.0.0.1". If so, leave it as empty string, false or null.
     *      @see http://stackoverflow.com/questions/1134290/cookies-on-localhost-with-explicit-domain
     *      @see http://php.net/manual/en/function.setcookie.php#73107
     *
     * COOKIE_PATH: The path where the cookie is valid for. If set to '/', the cookie will be available within the entire COOKIE_DOMAIN.
     * COOKIE_SECURE: If the cookie will be transferred through secured connection(SSL). It's highly recommended to set it to true if you have secured connection
     * COOKIE_HTTP: If set to true, Cookies that can't be accessed by JS - Highly recommended!
     * COOKIE_SECRET_KEY: A random value to make the cookie more secure.
     *
     */
    "COOKIE_EXPIRY" => 1209600,
    "SESSION_COOKIE_EXPIRY" => 604800,
    "COOKIE_DOMAIN" => '',
    "COOKIE_PATH" => '/',
    "COOKIE_SECURE" => false,
    "COOKIE_HTTP" => true,
    "COOKIE_SECRET_KEY" => "af&70-GF^!a{f64r5@g38l]#kQ4B+43%",

    /**
     * Configuration for: Encryption Keys
     *
     */
    "ENCRYPTION_KEY" => "3¥‹a0cd@!$251Êìcef08%&",
    "HMAC_SALT" => "a8C7n7^Ed0%8Qfd9K4m6d$86Dab",
    "HASH_KEY" => "z4D8Mp7Jm5cH",

    /**
     * Configuration for: Email server credentials
     * Emails are sent using SMTP, Don't use built-in mail() function in PHP.
     *
     */
    "EMAIL_SMTP_DEBUG" => 1,
    "EMAIL_SMTP_AUTH" => false,
    "EMAIL_SMTP_SECURE" => "tls",
    "EMAIL_SMTP_HOST" => "imap.gmail.com",
    "EMAIL_MAILER" => "imap",
    "EMAIL_SMTP_PORT" => 587,
    "EMAIL_SMTP_OPTION" => array(
             'tls' => array(
                 'verify_peer' => false,
                 'verify_peer_name' => false,
                 'allow_self_signed' => true
             )
         ),
    "EMAIL_SMTP_USERNAME" => "your smtp email",
    "EMAIL_SMTP_PASSWORD" => "your smtp password",
    "EMAIL_FROM" => "info@minicrociereisoleeolie.com",
    "EMAIL_FROM_NAME" => "YOUR WEB SITE",
    "EMAIL_REPLY_TO" => "your smtp email",
    "EMAIL_REPLY_NAME" => "REPLY TO",
    "ADMIN_EMAIL" => "your admin email",

    /**
     * Configuration for: Email Verification
     *
     * EMAIL_EMAIL_VERIFICATION_URL: Full URL must be provided
     *
     */
    "EMAIL_EMAIL_VERIFICATION" => "1",
    "EMAIL_EMAIL_VERIFICATION_URL" => PUBLIC_ROOT . "login/verifyUser",
    "EMAIL_EMAIL_VERIFICATION_SUBJECT" => "[IMP] Please verify your account",

    /**
     * Configuration for: Revoke email change
     *
     * EMAIL_REVOKE_EMAIL_URL: Full URL must be provided
     *
     */
    "EMAIL_REVOKE_EMAIL" => "2",
    "EMAIL_REVOKE_EMAIL_URL" => PUBLIC_ROOT . "User/revokeEmail",
    "EMAIL_REVOKE_EMAIL_SUBJECT" => "[IMP] Your email has been changed",

    /**
     * Configuration for: Confirm pending(updated) email
     *
     * EMAIL_UPDATE_EMAIL_URL: Full URL must be provided
     *
     */
    "EMAIL_UPDATE_EMAIL" => "3",
    "EMAIL_UPDATE_EMAIL_URL" => PUBLIC_ROOT . "User/updateEmail",
    "EMAIL_UPDATE_EMAIL_SUBJECT" => "[IMP] Please confirm your new email",

    /**
     * Configuration for: Reset Password
     *
     * EMAIL_PASSWORD_RESET_URL: Full URL must be provided
     *
     */
    "EMAIL_PASSWORD_RESET" => "4",
    "EMAIL_PASSWORD_RESET_URL" => PUBLIC_ROOT . "Login/resetPassword",
    "EMAIL_PASSWORD_RESET_SUBJECT" => "[IMP] Reset your password",

    /**
     * Configuration for: Report Bug, Feature, or Enhancement
     */
    "EMAIL_REPORT_BUG" => "5",
    "EMAIL_REPORT_BUG_SUBJECT" => "Request",

    "EMAIL_BUSSINESS" => "your paypal email",

    /**
     * Configuration for: Hashing strength
     *
     * It defines the strength of the password hashing/salting. "10" is the default value by PHP.
     * @see http://php.net/manual/en/function.password-hash.php
     *
     */
    "HASH_COST_FACTOR" => "20",

    /**
     * Configuration for: Pagination
     *
     */
    "PAGINATION_DEFAULT_LIMIT" => 9

);
