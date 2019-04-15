<?php
/**
 * @file mailgun.api.php
 * @date 2019-04-12
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Mailgun REST API interface module
 * @documentation https://documentation.mailgun.com/en/latest/api-sending.html
 */

if(!check_function_exists("mailgun_get_config")) {
  function mailgun_get_config() {
    $config = get_config();
    
    return array(
      "domain" => get_value_in_array("mailgun_domain", $config, ""),
      "name" => get_value_in_array("mailgun_name", $config, "John Doe"),
      "from" => get_value_in_array("mailgun_from", $config, ""),
      "apikey" => get_value_in_array("mailgun_apikey", $config, ""),
    );
  }
}

if(!check_function_exists("mailgun_send_message")) {
  function mailgun_send_message($content, $to, $subject="Untitled") {
    $response = false;

    // get mailgun configuration
    $cnf = mailgun_get_config();

    // make request
    if(loadHelper("webpagetool")) {
      $response = get_web_json(sprintf("https://api.mailgun.net/v3/%s/messages", $domain), array(
        "headers" => array(
          "Content-Type" => "multipart/form-data",
          "Authentication" => array("Basic", "api", $cnf['apikey']),
        ),
        "data" => array(
          "from" => sprintf("%s <%s>", $cnf['name'], $cnf['from']),
          "to" => $to,
          "subject" => $subject,
          "text" => $content,
        ),
      ));
    }

    return $response;
  }
}
