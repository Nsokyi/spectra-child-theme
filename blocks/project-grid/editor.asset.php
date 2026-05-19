<?php
return array(
    'dependencies' => array(
        'wp-blocks',
        'wp-element',
        'wp-block-editor',
        'wp-components',
        'wp-server-side-render',
        'wp-api-fetch',
    ),
    'version' => file_exists(__DIR__ . '/editor.js') ? filemtime(__DIR__ . '/editor.js') : '0.0.0',
);
