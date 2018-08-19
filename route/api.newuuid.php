<?php
/**
 * @file api.newuuid.php
 * @date 2018-08-19
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief UUID Generator API
 */

loadHelper("UUID.class");

$uuid = UUID::v4();
echo get_callable_token($uuid);
