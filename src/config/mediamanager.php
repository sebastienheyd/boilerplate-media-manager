<?php

return [
    'authorized' => [
        'size'  => '2048',
        'mimes' => [ // Authorized mimes to upload (see : https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types)
                     'jpg',
                     'jpeg',
                     'png',
                     'gif',
                     'svg',
                     'pdf',
                     'doc',
                     'docx',
                     'odt',
                     'xls',
                     'xlsx',
                     'ods',
                     'ppt',
                     'pptx',
                     'zip',
                     'rar',
                     'txt',
                     'mp3',
                     'wav',
                     'ogg',
                     'mkv',
                     'mp4',
                     'avi',
                     'wmv',
        ],
    ],
    'filetypes'  => [ // Recognized filetypes
                      'image'   => 'png|jpg|jpeg|gif|svg',
                      'word'    => 'doc|docx|odt',
                      'excel'   => 'xls|xlsx|ods',
                      'ppt'     => 'ppt|pptx',
                      'pdf'     => 'pdf',
                      'code'    => 'php|js|java|python|ruby|go|c|cpp|sql|m|h|json|html|aspx',
                      'archive' => 'zip|tar\.gz|rar|rpm',
                      'txt'     => 'txt|pac|log|md',
                      'audio'   => 'mp3|wav|flac|3pg|aa|aac|ape|au|m4a|mpc|ogg',
                      'video'   => 'mkv|rmvb|flv|mp4|avi|wmv|rm|asf|mpeg',
    ],
    'icons'      => [ // Icons linked to filetypes
                      'file'    => 'file-o', // default
                      'image'   => 'file-image-o',
                      'word'    => 'file-word-o',
                      'excel'   => 'file-excel-o',
                      'ppt'     => 'file-powerpoint-o',
                      'pdf'     => 'file-pdf-o',
                      'code'    => 'file-code-o',
                      'archive' => 'file-zip-o',
                      'txt'     => 'file-text-o',
                      'audio'   => 'file-audio-o',
                      'video'   => 'file-video-o',
    ],
    'filter'     => [
        '.gitignore',
        '.git',
        '.htaccess'
    ]
];