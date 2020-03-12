<?php

/** @var modX $modx */
switch ($modx->event->name) {
  case 'OnHandleRequest':

    if (!isset($_COOKIE['original_ref']))
      $_COOKIE['original_ref'] = isset($_SERVER['HTTP_REFERER']) ?
        $_SERVER['HTTP_REFERER'] : '';

    if (!isset($_COOKIE['start_page']))
      $_COOKIE['start_page'] = (isset($_SERVER["HTTPS"]) ? 'https://' : 'http://') . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];

    if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && $_SERVER["HTTP_X_FORWARDED_FOR"] != "")
      $_COOKIE['ip'] = $_SERVER["HTTP_X_FORWARDED_FOR"];
    else
      $_COOKIE['ip'] = $_SERVER["REMOTE_ADDR"];

    $_COOKIE['url'] =  (isset($_SERVER["HTTPS"]) ? 'https://' : 'http://') . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    $utmmark_marks = $modx->getOption('utmmark_marks');
    $fields = explode(",", $utmmark_marks);

    $cookie_field = '';
    foreach ($fields as $id => $field) {
      $field = trim($field);
      if (isset($_GET[$field]) && $_GET[$field] != '')
        $cookie_field = htmlspecialchars($_GET[$field], ENT_QUOTES, 'UTF-8');
      elseif (isset($_COOKIE[$field]) && $_COOKIE[$field] != '') {
        $cookie_field = $_COOKIE[$field];
      } else {
        $cookie_field = '';
      }

      $domain = $_SERVER["SERVER_NAME"];
      if (strtolower(substr($domain, 0, 4)) == 'www.') $domain = substr($domain, 4);
      if (substr($domain, 0, 1) != '.' && $domain != "localhost") $domain = '.' . $domain;

      #organic start
    
      if ($field == 'utm_source') {
        $utm_source = $cookie_field;
        if (empty($utm_source)) {
          $original_ref = $_COOKIE['original_ref'];

          switch ($original_ref) {
            case 'https://www.google.ru/':
              $cookie_field = 'google';
              break;
            case 'https://yandex.ru/':
              $cookie_field = 'yandex';
              break;
          }
        }
      }

      if ($field == 'utm_campaign') {
        $utm_campaign = $cookie_field;
        if (empty($utm_campaign)) {
          $original_ref = $_COOKIE['original_ref'];

          switch ($original_ref) {
            case 'https://www.google.ru/':
            case 'https://yandex.ru/':
              $cookie_field = 'organic';
              break;
          }
        }
      }

      #organic end
      setcookie($field, $cookie_field, time() + 60 * 60 * 24, '/', $domain);

      $_COOKIE[$field] = $cookie_field;

      $_COOKIE[$field] =  $_COOKIE[preg_replace("/_i$/", "", $field)];

      $modx->setPlaceholders(array(
        $field => $_COOKIE[$field],
      ), 'ref.');
    }
    break;
}
