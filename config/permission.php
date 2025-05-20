<?php

return [
    "web" => [
        "type" => "group",
        "name" => "web",
        "description" => "Web Group",
        "actions" => [],
        "permissions" => [],
        "children" => [
            "post" => [
                "type" => "resource",
                "name" => "post",
                "description" => "Post Management",
                "actions" => [
                    "create" => "Create post",
                    "delete" => "Delete post",
                ],
                "permissions" => [
                    0 => "web:post:create",
                    1 => "web:post:delete",
                ],
                "children" => [
                    "comment" => [
                        "type" => "resource",
                        "name" => "comment",
                        "description" => "Comment Management",
                        "actions" => [
                            "create" => "Create comment",
                            "delete" => "Delete comment",
                        ],
                        "permissions" => [
                            0 => "web:post.comment:create",
                            1 => "web:post.comment:delete",
                        ],
                        "children" => [],
                    ],
                ],
            ],
        ],
    ],
    "api" => [
        "type" => "group",
        "name" => "api",
        "description" => "Api Group",
        "actions" => [],
        "permissions" => [],
        "children" => [
            "news" => [
                "type" => "resource",
                "name" => "news",
                "description" => "News Management",
                "actions" => [
                    "update" => "Update existing news",
                    "delete" => "Delete news",
                ],
                "permissions" => [
                    0 => "api:news:update",
                    1 => "api:news:delete",
                ],
                "children" => [
                    "comment" => [
                        "type" => "resource",
                        "name" => "comment",
                        "description" => "Comment Management",
                        "actions" => [
                            "create" => "Create comment",
                            "delete" => "Delete comment",
                        ],
                        "permissions" => [
                            0 => "api:news.comment:create",
                            1 => "api:news.comment:delete",
                        ],
                        "children" => [],
                    ],
                ],
            ],
        ],
    ],
];
