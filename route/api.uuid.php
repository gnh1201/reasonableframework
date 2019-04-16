<?php
/**
 * @file api.uuid.php
 * @date 2018-08-19
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief UUID Generator API
 */

if(!defined("_DEF_RSF_")) set_error_exit("do not allow access");

loadHelper("UUID.class");

$uuid = UUID::v4();
echo get_callable_token($uuid);
