<?php

return [
    'encoding'         => 'UTF-8',
    'finalize'         => true,
    'ignoreNonStrings' => false,
    'cachePath'        => storage_path('app/purifier'),
    'cacheFileMode'    => 0755,
    'settings'         => [
        'announcements' => [
            'HTML.Doctype'             => 'HTML 4.01 Transitional',
            'HTML.Allowed'             => 'h1,h2,h3,h4,h5,h6,p,br,strong,b,em,i,u,ul,ol,li,a[href|title],span[style],div[style],img[src|alt|width|height]',
            'CSS.AllowedProperties'    => 'font-weight,font-style,text-decoration,text-align,color,background-color',
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.RemoveEmpty'   => true,
            'Core.Encoding'            => 'UTF-8',
        ],
    ],
];


