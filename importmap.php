<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    'bootstrap' => [
        'version' => '5.3.2',
    ],
    '@popperjs/core' => [
        'version' => '2.11.8',
    ],
    'dropzone/dist/dropzone.css' => [
        'version' => '6.0.0-beta.2',
        'type' => 'css',
    ],
    'plyr' => [
        'version' => '3.7.8',
    ],
    'plyr/dist/plyr.min.css' => [
        'version' => '3.7.8',
        'type' => 'css',
    ],
    'bootstrap/dist/js/bootstrap.min.js' => [
        'version' => '5.3.2',
    ],
    'overlayscrollbars' => [
        'version' => '2.4.5',
    ],
    'choices' => [
        'version' => '0.1.3',
    ],
    'colors' => [
        'version' => '1.4.0',
    ],
    'tiny-slider' => [
        'version' => '2.9.4',
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    '@hotwired/turbo' => [
        'version' => '7.3.0',
    ],
];
