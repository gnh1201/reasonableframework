<?php
if(!defined("_DEF_RSF_")) set_error_exit("do not allow access");

$data = array(
  "name" => "Hong gil dong",
  "email" => "support@exts.kr",
  "tel" => ""01000000000",
  "base_url" => base_url()
);

renderView("view_payman.example.php", $data);
