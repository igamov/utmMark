<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;
    /** @var Office $office */
    if ($Office = $modx->getService('Office', 'Office', MODX_CORE_PATH . 'components/office/model/office/')) {
        if (!($Office instanceof Office)) {
            $modx->log(xPDO::LOG_LEVEL_ERROR, '[utmMark] Could not register paths for Office component!');

            return true;
        } elseif (!method_exists($Office, 'addExtension')) {
            $modx->log(xPDO::LOG_LEVEL_ERROR,
                '[utmMark] You need to update Office for support of 3rd party packages!');

            return true;
        }

        /** @var array $options */
        switch ($options[xPDOTransport::PACKAGE_ACTION]) {
            case xPDOTransport::ACTION_INSTALL:
            case xPDOTransport::ACTION_UPGRADE:
                $Office->addExtension('utmMark', '[[++core_path]]components/utmmark/controllers/office/');
                $modx->log(xPDO::LOG_LEVEL_INFO, '[utmMark] Successfully registered utmMark as Office extension!');
                break;

            case xPDOTransport::ACTION_UNINSTALL:
                $Office->removeExtension('utmMark');
                $modx->log(xPDO::LOG_LEVEL_INFO, '[utmMark] Successfully unregistered utmMark as Office extension.');
                break;
        }
    }
}

return true;