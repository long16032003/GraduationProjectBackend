<?php return array (
  'tree' => 
  array (
    'web' => 
    array (
      'type' => 'group',
      'name' => 'web',
      'description' => 'Web',
      'actions' => 
      array (
      ),
      'children' => 
      array (
        'post' => 
        array (
          'type' => 'resource',
          'name' => 'post',
          'description' => 'Post',
          'actions' => 
          array (
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Create Post',
              'permission' => 'web:post:create',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Delete Post',
              'permission' => 'web:post:delete',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
            'comment' => 
            array (
              'type' => 'resource',
              'name' => 'comment',
              'description' => 'Comment',
              'actions' => 
              array (
                'create' => 
                array (
                  'name' => 'create',
                  'description' => 'Create Comment',
                  'permission' => 'web:post.comment:create',
                  'type' => 'action',
                ),
                'delete' => 
                array (
                  'name' => 'delete',
                  'description' => 'Delete Comment',
                  'permission' => 'web:post.comment:delete',
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
    ),
    'api' => 
    array (
      'type' => 'group',
      'name' => 'api',
      'description' => 'Api Group',
      'actions' => 
      array (
      ),
      'children' => 
      array (
        'news' => 
        array (
          'type' => 'resource',
          'name' => 'news',
          'description' => 'News',
          'actions' => 
          array (
            'create' => 
            array (
              'name' => 'create',
              'description' => 'Create News',
              'permission' => 'api:news:create',
              'type' => 'action',
            ),
            'delete' => 
            array (
              'name' => 'delete',
              'description' => 'Delete News',
              'permission' => 'api:news:delete',
              'type' => 'action',
            ),
          ),
          'children' => 
          array (
            'comment' => 
            array (
              'type' => 'resource',
              'name' => 'comment',
              'description' => 'Comment',
              'actions' => 
              array (
                'create' => 
                array (
                  'name' => 'create',
                  'description' => 'Create Comment',
                  'permission' => 'api:news.comment:create',
                  'type' => 'action',
                ),
                'delete' => 
                array (
                  'name' => 'delete',
                  'description' => 'Delete Comment',
                  'permission' => 'api:news.comment:delete',
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
    ),
  ),
  'permissions' => 
  array (
    0 => 'web:post:create',
    1 => 'web:post:delete',
    2 => 'web:post.comment:create',
    3 => 'web:post.comment:delete',
    4 => 'api:news:create',
    5 => 'api:news:delete',
    6 => 'api:news.comment:create',
    7 => 'api:news.comment:delete',
  ),
);