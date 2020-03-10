<?php

/** @var modX $modx */
/** @var array $scriptProperties */
/** @var utmMark $utmMark */
$utmMark = $modx->getService('utmMark', 'utmMark', MODX_CORE_PATH . 'components/utmmark/model/', $scriptProperties);
if (!$utmMark) {
    return 'Could not load utmMark class!';
}
$pdo = $modx->getService('pdoTools');

// Do your snippet code here. This demo grabs 5 items from our custom table.
$tpl = $modx->getOption('tpl', $scriptProperties, 'tpl.utmMark.item');
$toPlaceholder = $modx->getOption('toPlaceholder', $scriptProperties, false);

$utmmark_marks = $modx->getOption('utmmark_marks');

$fields = explode(",", $utmmark_marks);
foreach ($fields as $field) {
    $field = trim($field);
    $value = $modx->getPlaceholder('ref.' . $field);
    $pls = array('field' => $field, 'value' => $value);
    $output .= $pdo->getChunk($tpl, $pls);
}

// Output
if (!empty($toPlaceholder)) {
    // If using a placeholder, output nothing and set output to specified placeholder
    $modx->setPlaceholder($toPlaceholder, $output);

    return '';
}
// By default just return output
return $output;