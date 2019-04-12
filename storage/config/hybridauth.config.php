<?php
if(!defined("_DEF_RSF_")) set_error_exit("do not allow access");

/**
 * HybridAuth
 * http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
 * (c) 2009-2015, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
 */
// ----------------------------------------------------------------------------------------
//    HybridAuth Config file: http://hybridauth.sourceforge.net/userguide/Configuration.html
// ----------------------------------------------------------------------------------------

$config = get_config();

return array(
    "base_url" => base_url(),
    "providers" => array(
        // openid providers
        "OpenID" => array(
            "enabled" => true,
        ),
        "Yahoo" => array(
            "enabled" => true,
            "keys" => array("id" => "", "secret" => ""),
        ),
        "AOL" => array(
            "enabled" => true,
        ),
        "Google" => array(
            "enabled" => true,
            "keys" => array(
                "id" => get_value_in_array("social_google_id", $config, ""),
                "secret" => get_value_in_array("social_google_secret", $config, ""),
            ),
        ),
        "Facebook" => array(
            "enabled" => true,
            "keys" => array(
                "id" => get_value_in_array("social_facebook_id", $config, ""),
                "secret" => get_value_in_array("social_facebook_secret", $config, ""),
            ),
            "trustForwarded" => false,
            "scope" => array("email", "public_profile"),
        ),
        "Twitter" => array(
            "enabled" => true,
            "keys" => array(
                "key" => get_value_in_array("social_twitter_key", $config, ""),
                "secret" => get_value_in_array("social_twitter_secret", $config, ""),
            ),
            "includeEmail" => false,
        ),
        // windows live
        "Live" => array(
            "enabled" => true,
            "keys" => array("id" => "", "secret" => ""),
        ),
        "LinkedIn" => array(
            "enabled" => true,
            "keys" => array(
                "id" => get_value_in_array("social_linkedin_id", $config, ""),
                "secret" => get_value_in_array("social_linkedin_secret", $config, ""),
            ),
            "fields" => array(),
        ),
        "Foursquare" => array(
            "enabled" => true,
            "keys" => array("id" => "", "secret" => ""),
        ),
    ),
    // If you want to enable logging, set 'debug_mode' to true.
    // You can also set it to
    // - "error" To log only error messages. Useful in production
    // - "info" To log info and error messages (ignore debug messages)
    "debug_mode" => true,
    // Path to file writable by the web server. Required if 'debug_mode' is not false
    "debug_file" => get_current_working_dir() . "/storage/logs/hybridauth.log",
);
