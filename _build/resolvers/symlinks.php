<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;

    $dev = MODX_BASE_PATH . 'Extras/utmMark/';
    /** @var xPDOCacheManager $cache */
    $cache = $modx->getCacheManager();
    if (file_exists($dev) && $cache) {
        if (!is_link($dev . 'assets/components/utmmark')) {
            $cache->deleteTree(
                $dev . 'assets/components/utmmark/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_ASSETS_PATH . 'components/utmmark/', $dev . 'assets/components/utmmark');
        }
        if (!is_link($dev . 'core/components/utmmark')) {
            $cache->deleteTree(
                $dev . 'core/components/utmmark/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_CORE_PATH . 'components/utmmark/', $dev . 'core/components/utmmark');
        }
    }
}

return true;