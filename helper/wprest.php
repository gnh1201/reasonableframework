<?php
/**
 * @file wprest.php
 * @date 2018-03-14
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Wordpress Rest API helper
 */

if(!check_function_exists("get_wp_posts")) {
    function get_wp_posts($wp_server_url) {
        $results = array();

        $posts = parse_wp_posts($wp_server_url);
        $url_res = parse_url($wp_server_url);
        $origin = $url_res['host'];

        foreach($posts as $post) {
            $title = $post['title'];
            $content = $post['content'];
            $link = $post['link'];
            $object_id = $post['id'];

            $new_message = get_wp_new_message($title, $content, $link);
            $alt_message = get_wp_new_message($title, $content);

            $results[] = array(
                "origin"           => $origin,
                "title"            => $title,
                "content"          => $content,
                "link"             => $link,
                "message"          => $new_message,
                "alt_message"      => $alt_message,
                "object_id"        => $object_id,
                "hash_title"       => get_hashed_text($title),
                "hash_content"     => get_hashed_text($content),
                "hash_link"        => get_hashed_text($link),
                "hash_message"     => get_hashed_text($new_message),
                "hash_alt_message" => get_hashed_text($alt_message)
            );
        }

        return $results;
    }
}

if(!check_function_exists("parse_wp_posts")) {
    function parse_wp_posts($wp_server_url) {
        $rest_no_route = false;

        $posts = array();
        $results = array();

        $response = get_web_json($wp_server_url, "get", array(
            "rest_route" => "/wp/v2/posts/"
        ));

        $code = get_value_in_object("code", $response);
        if($code === "rest_no_route") {
            $rest_no_route = true;
            $response = get_web_xml($wp_server_url, "get", array(
                "feed" => "rss2"
            ));
        }

        if($rest_no_route === false) {
            $posts = $response;
            foreach($posts as $post) {        
                $results[] = array(
                    "title" => get_clean_xss($post->title->rendered, 1),
                    "content" => get_clean_xss($post->content->rendered, 1),
                    "link" => get_clean_xss($post->guid->rendered, 1),
                    "id" => $post->id,
                );
            }
        } else {
            $posts = $response->channel->item;
            foreach($posts as $post) {
                $post_link = get_clean_xss($post->link);
                $post_link_paths = array_filter(explode("/", $post_link), "strlen");
                $results[] = array(
                    "title" => get_clean_xss($post->title),
                    "content" => get_clean_xss($post->description),
                    "link" => $post_link,
                    "id" => end($post_link_paths),
                );
            }
        }

        return $results;
    }
}

if(!check_function_exists("get_wp_new_message")) {
    function get_wp_new_message($title, $content, $link="") {
        $new_message = "";

        $clean_title = get_clean_text($title);
        $clean_content = get_clean_text($content);
        $clean_llnk = get_clean_text($link);

        $message = $clean_title . " \n" . $clean_content;
        $words = explode(' ', $message);
        $words_choice = array_slice($words, 0, 30);
        $new_message = trim(implode(' ', $words_choice));

        if(!empty($clean_llnk)) {
            $new_message .= " " . $clean_llnk;
        }

        return $new_message;
    }
}

if(!check_function_exists("authenticate_wp")) {
    function authenticate_wp($wp_server_url, $client_id, $client_secret, $route="", $code="", $scope="basic", $state="") {
            $flag = false;

            $wp_access_token = get_session("wp_access_token");
            $result = array(
                "redirect_uri" => false,
                "response" => false
            );

            if(empty($wp_access_token)) {
                if(empty($code)) {
                    // step 1
                    $redirect_uri = get_web_build_qs($wp_server_url . "/oauth/authorize", array(
                        "client_id" => $client_id,
                        "redirect_uri" => get_route_link($route),
                        "response_type" => "code",
                        "scope" => $scope,
                        "state" => $state
                    ));
                    $result['redirect_uri'] = $redirect_uri;
                } else {
                    // step 2
                    $response = get_web_json($wp_server_url . "/oauth/token/", "jsondata", array(
                        "headers" => array(
                            "Content-Type" => "application/x-www-form-urlencoded",
                            "Authorization" => sprintf("Basic %s", base64_encode($client_id . ":" . $client_secret))
                        ),
                        "data" => array(
                            "grant_type" => "authorization_code",
                            "code" => $code,
                            "client_id" => $client_id,
                            "client_secret" => $client_secret,
                            "redirect_uri" => get_route_link($route),
                            "state" => $state
                        )
                    ));

                    // store access token to session
                    set_session("wp_access_token", $response->access_token);
                    set_session("wp_expires_in", $response->expires_in);
                    set_session("wp_token_type", $response->token_type);
                    set_session("wp_scope", $response->scope);
                    set_session("refresh_token", $response->refresh_token);

                    // store respose to result
                    $result['redirect_uri'] = get_route_link($route);
                    $result['response'] = $response;
                }

                if(!array_key_empty("redirect_uri", $result)) {
                    redirect_uri($result['redirect_uri']);
                }
            } else {
                $flag = true;
            }

            return $result;
    }
}

if(!check_function_exists("write_wp_post")) {
    function write_wp_post($wp_server_url, $access_token, $data=array()) {
        $default_data = array(
            "title" => "Untitled",
            "content" => "insert your content",
            "author" => 2,
            "status" => "publish",
            "categories" => ""
        );

        foreach($data as $k=>$v) {
            $default_data[$k] = $v;
        }

        $response = get_web_json(get_web_build_qs($wp_server_url, array(
                "rest_route" => "/wp/v2/posts"
            )), "jsondata", array(
                "headers" => array(
                    "Content-Type" => "application/x-www-form-urlencoded",
                    "Authorization" => "Bearer " . $access_token
                ),
                "data" => $default_data
            )
        );

        return $response;
    }
}

if(!check_function_exists("get_wp_categories")) {
    function get_wp_categories($wp_server_url, $access_token) {
        $response = get_web_json(get_web_build_qs($wp_server_url, array(
            "rest_route" => "/wp/v2/categories"
        )), "get");

        return $response;
    }
}
