<?php
    return array(
        // REST patterns
        array('api/list', 'pattern'=>'<model:(repository|package)>/api', 'verb'=>'GET'),
        array('api/view', 'pattern'=>'<model:repository>/<id:[\w\d-_]+>/api', 'verb'=>'GET'),
        array('api/view', 'pattern'=>'<model:package>/<type_id:[\w\d-_]+>/<id:[\w\d-_]+>/api', 'verb'=>'GET'),
        array('api/view', 'pattern'=>'<model:version>/<type_id:[\w\d-_]+>/<package_id:[\w\d-_]+>/<version:[\w\d\.-_]+>/api', 'verb'=>'GET'),
        'login'=>'site/login',
        'logout'=>'site/logout',
        'version/<action:(upload|send)>'=>'version/<action>',
        'repository/<repository:[\w\d-_]+>/download/<type:[\w\d-_]+>/<package:[\w\d-_]+>/<version:[\w\d\.-_]+>'=>'version/download',
        'repository/<repository:[\w\d-_]+>/info/<type:[\w\d-_]+>/<package:[\w\d-_]+>/<version:[\w\d\.-_]+>'=>'version/info',
        'repository/<repository:[\w\d-_]+>/icon/<type:[\w\d-_]+>/<package:[\w\d-_]+>/<version:[\w\d\.-_]+>'=>'package/displayIcon',
        'repository/<id:[\w\d-_]+>/icon'=>'repository/icon',
        'repository/<id:[\w\d-_]+>/data'=>'repository/data',
        '<controller:\w+>/<action:(admin|index|create)>'=>'<controller>/<action>',
        '<package:\w+>/<action:(update|delete)>/<type_id:[\w\d-_]+>/<id:[\w\d-_]+>'=>'<controller>/<action>',
        '<controller:\w+>/<action:(update|delete)>/<id:[\w\d-_]+>'=>'<controller>/<action>',
        '<controller:\w+>/<id:[\w\d-_]+>'=>'<controller>/view',
    );
?>