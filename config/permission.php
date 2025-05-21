<?php return array (
  'tree' => 
  array (
    'default' => 
    array (
      'type' => 'group',
      'name' => 'default',
      'description' => 'Default',
      'actions' => 
      array (
      ),
      'children' => 
      array (
        'user' => 
        array (
          'type' => 'resource',
          'name' => 'user',
          'description' => 'User',
          'actions' => 
          array (
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Browse',
              'permission' => 'user:browse',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'View',
              'permission' => 'user:read',
              'type' => 'action',
            ),
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Create',
              'permission' => 'user:create',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Update',
              'permission' => 'user:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Delete',
              'permission' => 'user:delete',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Replicate',
              'permission' => 'user:clone',
              'type' => 'action',
            ),
            'import' => 
            array (
              'name' => 'import',
              'description' => 'Import',
              'permission' => 'user:import',
              'type' => 'action',
            ),
            'export' => 
            array (
              'name' => 'export',
              'description' => 'Export',
              'permission' => 'user:export',
              'type' => 'action',
            ),
            'print' => 
            array (
              'name' => 'print',
              'description' => 'Print',
              'permission' => 'user:print',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
          ),
        ),
        'role' => 
        array (
          'type' => 'resource',
          'name' => 'role',
          'description' => 'Role',
          'actions' => 
          array (
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Browse',
              'permission' => 'role:browse',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'View',
              'permission' => 'role:read',
              'type' => 'action',
            ),
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Create',
              'permission' => 'role:create',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Update',
              'permission' => 'role:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Delete',
              'permission' => 'role:delete',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Replicate',
              'permission' => 'role:clone',
              'type' => 'action',
            ),
            'import' => 
            array (
              'name' => 'import',
              'description' => 'Import',
              'permission' => 'role:import',
              'type' => 'action',
            ),
            'export' => 
            array (
              'name' => 'export',
              'description' => 'Export',
              'permission' => 'role:export',
              'type' => 'action',
            ),
            'print' => 
            array (
              'name' => 'print',
              'description' => 'Print',
              'permission' => 'role:print',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
          ),
        ),
        'media' => 
        array (
          'type' => 'resource',
          'name' => 'media',
          'description' => 'Media',
          'actions' => 
          array (
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Browse',
              'permission' => 'media:browse',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'View',
              'permission' => 'media:read',
              'type' => 'action',
            ),
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Upload',
              'permission' => 'media:create',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Update',
              'permission' => 'media:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Delete',
              'permission' => 'media:delete',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Replicate',
              'permission' => 'media:clone',
              'type' => 'action',
            ),
            'import' => 
            array (
              'name' => 'import',
              'description' => 'Import',
              'permission' => 'media:import',
              'type' => 'action',
            ),
            'export' => 
            array (
              'name' => 'export',
              'description' => 'Export',
              'permission' => 'media:export',
              'type' => 'action',
            ),
            'print' => 
            array (
              'name' => 'print',
              'description' => 'Print',
              'permission' => 'media:print',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
          ),
        ),
      ),
    ),
  ),
  'flat' => 
  array (
    0 => 'user:browse',
    1 => 'user:read',
    2 => 'user:create',
    3 => 'user:update',
    4 => 'user:delete',
    5 => 'user:clone',
    6 => 'user:import',
    7 => 'user:export',
    8 => 'user:print',
    9 => 'role:browse',
    10 => 'role:read',
    11 => 'role:create',
    12 => 'role:update',
    13 => 'role:delete',
    14 => 'role:clone',
    15 => 'role:import',
    16 => 'role:export',
    17 => 'role:print',
    18 => 'media:browse',
    19 => 'media:read',
    20 => 'media:create',
    21 => 'media:update',
    22 => 'media:delete',
    23 => 'media:clone',
    24 => 'media:import',
    25 => 'media:export',
    26 => 'media:print',
  ),
);