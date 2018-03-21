<?php
/**
 * @file wprest.php
 * @date 2018-03-14
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Wordpress Rest API helper
 */
 
function get_wp_posts($wp_server_url) {
	$new_posts = array();

	$response = get_web_json($wp_server_url, "get", array(
		"rest_route" => "/wp/v2/posts/"
	));

	$url_res = parse_url($wp_server_url);
	$origin = $url_res['host'];
	$response = is_array($response) ? $response : array();

	foreach($response as $post) {
		$title = get_clean_xss($post->title->rendered, 1);
		$content = get_clean_xss($post->content->rendered, 1);
		$link = get_clean_xss($post->guid->rendered, 1);

		$new_message = get_wp_new_message($title, $content, $link);
		$alt_message = get_wp_new_message($title, $content);

		$new_posts[] = array(
			"origin"           => $origin,
			"title"            => $title,
			"content"          => $content,
			"link"             => $link,
			"message"          => $new_message,
			"alt_message"      => $alt_message,
			"object_id"        => $post->id,
			"hash_title"       => get_hashed_text($title),
			"hash_content"     => get_hashed_text($content),
			"hash_link"        => get_hashed_text($link),
			"hash_message"     => get_hashed_text($message),
			"hash_alt_message" => get_hashed_text($alt_message)
		);
	}

	return $new_posts;
}

function get_wp_new_message($title, $content, $link="") {
	$new_message = "";

	$clean_title = get_clean_text($title);
	$clean_content = get_clean_text($content);
	$clean_llnk = get_clean_text($link);

	$message = $clean_title . '. ' . $clean_content;
	$words = explode(' ', $message);
	$words_choice = array_slice($words, 0, 30);
	$new_message = implode(' ', $words_choice);

	if(!empty($clean_llnk)) {
		$new_message .= " " . $clean_llnk;
	}

	return $new_message;
}
