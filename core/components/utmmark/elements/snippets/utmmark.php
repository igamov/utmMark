<?php
/** @var modX $modx */
/** @var array $scriptProperties */
/** @var utmMark $utmMark */
$utmMark = $modx->getService('utmMark', 'utmMark', MODX_CORE_PATH . 'components/utmmark/model/', $scriptProperties);
if (!$utmMark) {
    return 'Could not load utmMark class!';
}
$pdo = $modx->getService('pdoTools');
if (!$pdo) {
    return 'Could not load pdoTools class!';
}

$tpl = $modx->getOption('tpl', $scriptProperties, 'tpl.utmMark.item');
$toPlaceholder = $modx->getOption('toPlaceholder', $scriptProperties, false);

$utmmark_marks = $modx->getOption('utmmark_marks');

if(!$utmmark_marks){
    return 'Could not UTM tags';
}

$fields = explode(",", $utmmark_marks);

if(!is_array($fields)){
    $modx->log(1, 'Error utmMark. Fields is not array');
    return;
}

foreach ($fields as $field) {
    $field = trim($field);
    $value = $modx->getPlaceholder('ref.' . $field);
    $pls = array('field' => $field, 'value' => $value);
    $output .= $pdo->getChunk($tpl, $pls);
};

// Output
if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder, $output);
    return;
}
return $output;