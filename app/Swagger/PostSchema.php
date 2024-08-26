<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *     schema="Post",
 *     required={"title", "slug", "content"},
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="The title of the post"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="The slug of the post"
 *     ),
 *     @OA\Property(
 *         property="excerpt",
 *         type="string",
 *         description="A brief excerpt of the post",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="content",
 *         type="string",
 *         description="The content of the post"
 *     ),
 *     @OA\Property(
 *         property="meta_title",
 *         type="string",
 *         description="Meta title for the post",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="meta_description",
 *         type="string",
 *         description="Meta description for the post",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="meta_keywords",
 *         type="string",
 *         description="Meta keywords for the post",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="featured_image",
 *         type="string",
 *         description="URL of the featured image",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="ID of the user who created the post"
 *     )
 * )
 */
