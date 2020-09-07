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
    if (!$utmmark_marks) {
      return 'Could not UTM tags';
    }
    $fields = explode(",", $utmmark_marks);

    $cookie_field = '';
    foreach ($fields as $id => $field) {
      $field = trim($field);
      if (isset($_GET[$field]) && $_GET[$field] != '') {
        $cookie_field = str_replace("{", "", htmlspecialchars($_GET[$field], ENT_QUOTES, 'UTF-8'));
        $cookie_field = str_replace("}", "", $cookie_field);
      } elseif (isset($_COOKIE[$field]) && $_COOKIE[$field] != '') {
        $cookie_field = str_replace("{", "", $_COOKIE[$field]);
        $cookie_field = str_replace("}", "", $cookie_field);
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
  case 'msOnBeforeCreateOrder':
    $utmmark_marks = $modx->getOption('utmmark_marks');
    $address = $msOrder->getOne('Address');
    $properties = array();

    $fields = explode(",", $utmmark_marks);

    if (!is_array($fields)) {
      $modx->log(1, 'Error utmMark. Fields is not array');
      return;
    }

    foreach ($fields as $field) {
      $field = trim($field);
      $value = $modx->getPlaceholder('ref.' . $field);
      if($value){
        $properties[$field] = htmlentities($value, ENT_COMPAT | ENT_HTML401, 'UTF-8');
      }
    };

    if (count($properties) > 0) {
      $address->set('properties', json_encode($properties));
    }
    break;
  case 'msOnManagerCustomCssJs':
    if ($page != 'orders') return;
    $modx->controller->addHtml("
      <script type='text/javascript'>
        Ext.ComponentMgr.onAvailable('minishop2-window-order-update', function(){
          if (miniShop2.config['order_address_fields'].in_array('properties')){
            if (this.record.addr_properties){
              var key;
              for (key in this.record.addr_properties) {
                this.fields.items[2].items.push({
                  xtype: 'displayfield',
                  name: 'addr_properties_'+key,
                  fieldLabel: key,
                  anchor: '100%',
                  style: 'border:1px solid #efefef;width:95%;padding:5px;',
                  html: this.record.addr_properties[key]
                });
              }
            }		
          }
        });                
      </script>");
    break;
}