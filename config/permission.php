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
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Create User',
              'permission' => 'default:user:create',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'View User',
              'permission' => 'default:user:read',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Update User',
              'permission' => 'default:user:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Delete User',
              'permission' => 'default:user:delete',
              'type' => 'action',
            ),
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Browse User',
              'permission' => 'default:user:browse',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Replicate User',
              'permission' => 'default:user:clone',
              'type' => 'action',
            ),
            'restore' => 
            array (
              'name' => 'restore',
              'description' => 'Restore User',
              'permission' => 'default:user:restore',
              'type' => 'action',
            ),
            'forceDelete' => 
            array (
              'name' => 'forceDelete',
              'description' => 'Permanently delete User',
              'permission' => 'default:user:forceDelete',
              'type' => 'action',
            ),
            'export' => 
            array (
              'name' => 'export',
              'description' => 'Export User',
              'permission' => 'default:user:export',
              'type' => 'action',
            ),
            'import' => 
            array (
              'name' => 'import',
              'description' => 'Import User',
              'permission' => 'default:user:import',
              'type' => 'action',
            ),
            'print' => 
            array (
              'name' => 'print',
              'description' => 'Print User',
              'permission' => 'default:user:print',
              'type' => 'action',
            ),
            'approve' => 
            array (
              'name' => 'approve',
              'description' => 'Approve User',
              'permission' => 'default:user:approve',
              'type' => 'action',
            ),
            'reject' => 
            array (
              'name' => 'reject',
              'description' => 'Reject User',
              'permission' => 'default:user:reject',
              'type' => 'action',
            ),
            'upload' => 
            array (
              'name' => 'upload',
              'description' => 'Upload User',
              'permission' => 'default:user:upload',
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
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Create Role',
              'permission' => 'default:role:create',
              'type' => 'action',
            ),
            'read' => 
            array (
              'name' => 'read',
              'description' => 'View Role',
              'permission' => 'default:role:read',
              'type' => 'action',
            ),
            'update' => 
            array (
              'name' => 'update',
              'description' => 'Update Role',
              'permission' => 'default:role:update',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Delete Role',
              'permission' => 'default:role:delete',
              'type' => 'action',
            ),
            'browse' => 
            array (
              'name' => 'browse',
              'description' => 'Browse Role',
              'permission' => 'default:role:browse',
              'type' => 'action',
            ),
            'clone' => 
            array (
              'name' => 'clone',
              'description' => 'Replicate Role',
              'permission' => 'default:role:clone',
              'type' => 'action',
            ),
            'restore' => 
            array (
              'name' => 'restore',
              'description' => 'Restore Role',
              'permission' => 'default:role:restore',
              'type' => 'action',
            ),
            'forceDelete' => 
            array (
              'name' => 'forceDelete',
              'description' => 'Permanently delete Role',
              'permission' => 'default:role:forceDelete',
              'type' => 'action',
            ),
            'export' => 
            array (
              'name' => 'export',
              'description' => 'Export Role',
              'permission' => 'default:role:export',
              'type' => 'action',
            ),
            'import' => 
            array (
              'name' => 'import',
              'description' => 'Import Role',
              'permission' => 'default:role:import',
              'type' => 'action',
            ),
            'print' => 
            array (
              'name' => 'print',
              'description' => 'Print Role',
              'permission' => 'default:role:print',
              'type' => 'action',
            ),
            'approve' => 
            array (
              'name' => 'approve',
              'description' => 'Approve Role',
              'permission' => 'default:role:approve',
              'type' => 'action',
            ),
            'reject' => 
            array (
              'name' => 'reject',
              'description' => 'Reject Role',
              'permission' => 'default:role:reject',
              'type' => 'action',
            ),
            'upload' => 
            array (
              'name' => 'upload',
              'description' => 'Upload Role',
              'permission' => 'default:role:upload',
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
    0 => 'default:user:create',
    1 => 'default:user:read',
    2 => 'default:user:update',
    3 => 'default:user:delete',
    4 => 'default:user:browse',
    5 => 'default:user:clone',
    6 => 'default:user:restore',
    7 => 'default:user:forceDelete',
    8 => 'default:user:export',
    9 => 'default:user:import',
    10 => 'default:user:print',
    11 => 'default:user:approve',
    12 => 'default:user:reject',
    13 => 'default:user:upload',
    14 => 'default:role:create',
    15 => 'default:role:read',
    16 => 'default:role:update',
    17 => 'default:role:delete',
    18 => 'default:role:browse',
    19 => 'default:role:clone',
    20 => 'default:role:restore',
    21 => 'default:role:forceDelete',
    22 => 'default:role:export',
    23 => 'default:role:import',
    24 => 'default:role:print',
    25 => 'default:role:approve',
    26 => 'default:role:reject',
    27 => 'default:role:upload',
  ),
);