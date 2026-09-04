<?php

namespace App\PostTypes;

use Core\PostType;

class PagePost extends PostType
{
    public $_post_name = 'page';

    public function post_type_meta_args()
    {

        $meta_boxes[] = [
            'id'         => 'standard-home-options',
            'title'      => __('Options', LANG_STRING),
            'post_types' => 'page',
            'include'    => [
                'template' => 'page-home.php',
            ],
            'context'  => 'normal',
            'priority' => 'high',
            'autosave' => false,
            'tabs'     => [
                'intro'       => ['label' => __('Intro', LANG_STRING)],
                'featured'    => ['label' => __('Featured', LANG_STRING)],
                'collection'  => ['label' => __('Collection', LANG_STRING)],
                'banner'      => ['label' => __('Banner', LANG_STRING)],
                'certificate' => ['label' => __('Certificate', LANG_STRING)],
                'collector'   => ['label' => __('Collector', LANG_STRING)],
                'blog'        => ['label' => __('Blog', LANG_STRING)],
            ],
            'tab_style'   => 'default',
            'tab_wrapper' => true,
            'fields'      => [
                [
                    'name'   => __('Home', LANG_STRING),
                    'id'     => 'page',
                    'type'   => 'group',
                    'fields' => [
                        [
                            'name'   => __('Intro', LANG_STRING),
                            'id'     => 'intro',
                            'type'   => 'group',
                            'fields' => [
                                [
                                    'name'    => __('Section Title', LANG_STRING),
                                    'id'      => 'title',
                                    'type'    => 'textarea',
                                    'rows'    => 2,
                                    'columns' => 6,
                                ],
                                [
                                    'name'    => __('Section Subtitle', LANG_STRING),
                                    'id'      => 'subtitle',
                                    'type'    => 'textarea',
                                    'rows'    => 2,
                                    'columns' => 6,
                                ],
                                [
                                    'name'    => __('Section Image', LANG_STRING),
                                    'id'      => 'image',
                                    'type'    => 'single_image',
                                    'columns' => 6,
                                ],
                            ],
                            'columns' => 12,
                            'tab'     => 'intro',
                        ],
                        [
                            'name'   => __('Featured', LANG_STRING),
                            'id'     => 'featured',
                            'type'   => 'group',
                            'fields' => [
                                [
                                    'name'    => __('Section Title', LANG_STRING),
                                    'id'      => 'title',
                                    'type'    => 'textarea',
                                    'rows'    => 2,
                                    'columns' => 6,
                                ],
                                [
                                    'name'    => __('Section Subtitle', LANG_STRING),
                                    'id'      => 'subtitle',
                                    'type'    => 'textarea',
                                    'rows'    => 2,
                                    'columns' => 6,
                                ],
                            ],
                            'columns' => 12,
                            'tab'     => 'featured',
                        ],
                        [
                            'name'   => __('Collection', LANG_STRING),
                            'id'     => 'collection',
                            'type'   => 'group',
                            'fields' => [
                                [
                                    'name'    => __('Section Title', LANG_STRING),
                                    'id'      => 'title',
                                    'type'    => 'textarea',
                                    'rows'    => 2,
                                    'columns' => 6,
                                ],
                                [
                                    'name'          => __('Items', LANG_STRING),
                                    'id'            => 'item',
                                    'type'          => 'group',
                                    'clone'         => true,
                                    'sort_clone'    => true,
                                    'collapsible'   => true,
                                    'default_state' => 'collapsed',
                                    'group_title'   => '{#}- {title}',
                                    'fields'        => [
                                        [
                                            'name'    => __('Size', LANG_STRING),
                                            'id'      => 'size',
                                            'type'    => 'text',
                                            'columns' => 4,
                                        ],
                                        [
                                            'name'    => __('Title', LANG_STRING),
                                            'id'      => 'title',
                                            'type'    => 'text',
                                            'columns' => 4,
                                        ],
                                        [
                                            'name'       => __('Taxonomy', LANG_STRING),
                                            'id'         => 'taxonomy',
                                            'type'       => 'taxonomy_advanced',
                                            'taxonomy'   => ['pa_color', 'pa_origin', 'pa_design', 'pa_material', 'pa_shape', 'pa_thickness', 'pa_feel', 'product_cat'],
                                            'field_type' => 'select_advanced',
                                            'ajax'       => true,
                                            'columns'    => 4,
                                        ],
                                        [
                                            'name'    => __('Image', LANG_STRING),
                                            'id'      => 'image',
                                            'type'    => 'single_image',
                                            'columns' => 4,
                                        ],
                                        [
                                            'name'    => __('Description', LANG_STRING),
                                            'id'      => 'description',
                                            'type'    => 'wysiwyg',
                                            'options' => [
                                                'textarea_rows' => 3,
                                                'media_buttons' => false,
                                                'teeny'         => true,
                                            ],
                                            'columns' => 8,
                                        ],
                                    ],
                                    'columns' => 12,
                                ],
                            ],
                            'columns' => 12,
                            'tab'     => 'collection',
                        ],
                        [
                            'name'   => __('Banner', LANG_STRING),
                            'id'     => 'banner',
                            'type'   => 'group',
                            'fields' => [
                                [
                                    'name'    => __('Section Description', LANG_STRING),
                                    'id'      => 'description',
                                    'type'    => 'wysiwyg',
                                    'options' => [
                                        'textarea_rows' => 3,
                                        'media_buttons' => false,
                                        'teeny'         => true,
                                    ],
                                    'columns' => 6,
                                ],
                            ],
                            'columns' => 12,
                            'tab'     => 'banner',
                        ],
                        [
                            'name'   => __('Certificate', LANG_STRING),
                            'id'     => 'certificate',
                            'type'   => 'group',
                            'fields' => [
                                [
                                    'name'    => __('Section Title', LANG_STRING),
                                    'id'      => 'title',
                                    'type'    => 'textarea',
                                    'rows'    => 2,
                                    'columns' => 6,
                                ],
                                [
                                    'name'    => __('Section Subtitle', LANG_STRING),
                                    'id'      => 'subtitle',
                                    'type'    => 'textarea',
                                    'rows'    => 2,
                                    'columns' => 6,
                                ],
                                [
                                    'name'   => __('Button', LANG_STRING),
                                    'id'     => 'button',
                                    'type'   => 'group',
                                    'fields' => [
                                        [
                                            'name'    => __('Title', LANG_STRING),
                                            'id'      => 'title',
                                            'type'    => 'text',
                                            'columns' => 6,
                                        ],
                                        [
                                            'name'    => __('Link', LANG_STRING),
                                            'id'      => 'link',
                                            'type'    => 'url',
                                            'columns' => 6,
                                        ],
                                    ],
                                    'columns' => 6,
                                ],
                                [
                                    'name'    => __('Section Description', LANG_STRING),
                                    'id'      => 'description',
                                    'type'    => 'wysiwyg',
                                    'options' => [
                                        'textarea_rows' => 3,
                                        'media_buttons' => false,
                                        'teeny'         => true,
                                    ],
                                    'columns' => 6,
                                ],
                                [
                                    'name'          => __('Items', LANG_STRING),
                                    'id'            => 'item',
                                    'type'          => 'group',
                                    'clone'         => true,
                                    'sort_clone'    => true,
                                    'collapsible'   => true,
                                    'default_state' => 'collapsed',
                                    'group_title'   => '{#}- {title}',
                                    'fields'        => [
                                        [
                                            'name'    => __('Title', LANG_STRING),
                                            'id'      => 'title',
                                            'type'    => 'text',
                                            'columns' => 4,
                                        ],
                                        [
                                            'name'    => __('Link', LANG_STRING),
                                            'id'      => 'link',
                                            'type'    => 'url',
                                            'columns' => 4,
                                        ],
                                        [
                                            'name'    => __('Image', LANG_STRING),
                                            'id'      => 'image',
                                            'type'    => 'single_image',
                                            'columns' => 4,
                                        ],
                                    ],
                                    'columns' => 12,
                                ],
                            ],
                            'columns' => 12,
                            'tab'     => 'certificate',
                        ],
                        [
                            'name'   => __('Collector', LANG_STRING),
                            'id'     => 'collector',
                            'type'   => 'group',
                            'fields' => [
                                [
                                    'name'    => __('Section Title', LANG_STRING),
                                    'id'      => 'title',
                                    'type'    => 'textarea',
                                    'rows'    => 2,
                                    'columns' => 6,
                                ],
                                [
                                    'name'    => __('Section Subtitle', LANG_STRING),
                                    'id'      => 'subtitle',
                                    'type'    => 'textarea',
                                    'rows'    => 2,
                                    'columns' => 6,
                                ],
                            ],
                            'columns' => 12,
                            'tab'     => 'collector',
                        ],
                        [
                            'name'   => __('Blog', LANG_STRING),
                            'id'     => 'blog',
                            'type'   => 'group',
                            'fields' => [
                                [
                                    'name'    => __('Section Title', LANG_STRING),
                                    'id'      => 'title',
                                    'type'    => 'textarea',
                                    'rows'    => 2,
                                    'columns' => 6,
                                ],
                            ],
                            'columns' => 12,
                            'tab'     => 'blog',
                        ],
                    ],
                    'columns' => 12,
                ],
            ]
        ];

        $meta_boxes[] = [
            'id'         => 'standard-hero-options',
            'title'      => __('Hero', LANG_STRING),
            'post_types' => 'page',
            'context'    => 'normal',
            'priority'   => 'high',
            'autosave'   => false,
            'fields'     => [
                [
                    'name'   => __('Hero', LANG_STRING),
                    'id'     => 'hero',
                    'type'   => 'group',
                    'fields' => [
                        [
                            'name'    => __('Title', LANG_STRING),
                            'id'      => 'title',
                            'type'    => 'textarea',
                            'rows'    => 2,
                            'columns' => 4,
                        ],
                        [
                            'name'    => __('Subtitle', LANG_STRING),
                            'id'      => 'subtitle',
                            'type'    => 'textarea',
                            'rows'    => 2,
                            'columns' => 4,
                        ],
                        [
                            'name'    => __('Image', LANG_STRING),
                            'id'      => 'image',
                            'type'    => 'single_image',
                            'columns' => 4,
                        ],
                        [
                            'name'    => __('Description', LANG_STRING),
                            'id'      => 'description',
                            'type'    => 'wysiwyg',
                            'options' => [
                                'textarea_rows' => 3,
                                'media_buttons' => false,
                                'teeny'         => true,
                            ],
                            'columns' => 12,
                        ],
                    ],
                    'columns' => 12,
                    'tab'     => 'hero',
                ],
            ]
        ];

        $meta_boxes[] = [
            'id'         => 'standard-about-options',
            'title'      => __('Options', LANG_STRING),
            'post_types' => 'page',
            'include'    => [
                'template' => 'page-about.php',
            ],
            'context'  => 'normal',
            'priority' => 'high',
            'autosave' => false,
            'tabs'     => [
                'intro'       => ['label' => __('Intro', LANG_STRING)],
                'mission'     => ['label' => __('Mission', LANG_STRING)],
                'gallery'     => ['label' => __('Gallery', LANG_STRING)],
                'vision'      => ['label' => __('Vision', LANG_STRING)],
                'member'      => ['label' => __('Member', LANG_STRING)],
                'service'     => ['label' => __('Service', LANG_STRING)],
                'certificate' => ['label' => __('Certificate', LANG_STRING)],
                'cta'         => ['label' => __('CTA', LANG_STRING)],
            ],
            'tab_style'   => 'default',
            'tab_wrapper' => true,
            'fields'      => [
                [
                    'name'   => __('Page', LANG_STRING),
                    'id'     => 'page',
                    'type'   => 'group',
                    'fields' => [
                        [
                            'name'   => __('Intro', LANG_STRING),
                            'id'     => 'intro',
                            'type'   => 'group',
                            'fields' => [
                                [
                                    'name'    => __('Title', LANG_STRING),
                                    'id'      => 'title',
                                    'type'    => 'textarea',
                                    'rows'    => 2,
                                    'columns' => 6,
                                ],
                                [
                                    'name'    => __('Description', LANG_STRING),
                                    'id'      => 'description',
                                    'type'    => 'wysiwyg',
                                    'options' => [
                                        'textarea_rows' => 3,
                                        'media_buttons' => false,
                                        'teeny'         => true,
                                    ],
                                    'columns' => 12,
                                ],
                            ],
                            'columns' => 12,
                            'tab'     => 'intro',
                        ],
                        [
                            'name'   => __('Mission', LANG_STRING),
                            'id'     => 'mission',
                            'type'   => 'group',
                            'fields' => [
                                [
                                    'name'    => __('Title', LANG_STRING),
                                    'id'      => 'title',
                                    'type'    => 'textarea',
                                    'rows'    => 2,
                                    'columns' => 6,
                                ],
                                [
                                    'name'    => __('Description', LANG_STRING),
                                    'id'      => 'description',
                                    'type'    => 'wysiwyg',
                                    'options' => [
                                        'textarea_rows' => 3,
                                        'media_buttons' => false,
                                        'teeny'         => true,
                                    ],
                                    'columns' => 12,
                                ],
                            ],
                            'columns' => 12,
                            'tab'     => 'mission',
                        ],
                        [
                            'name'   => __('Gallery', LANG_STRING),
                            'id'     => 'gallery',
                            'type'   => 'group',
                            'fields' => [
                                [
                                    'name'    => __('Image', LANG_STRING),
                                    'id'      => 'image',
                                    'type'    => 'image_advanced',
                                    'columns' => 12,
                                ],
                            ],
                            'columns' => 12,
                            'tab'     => 'gallery',
                        ],
                        [
                            'name'   => __('Vision', LANG_STRING),
                            'id'     => 'vision',
                            'type'   => 'group',
                            'fields' => [
                                [
                                    'name'    => __('Title', LANG_STRING),
                                    'id'      => 'title',
                                    'type'    => 'textarea',
                                    'rows'    => 2,
                                    'columns' => 6,
                                ],
                                [
                                    'name'    => __('Description', LANG_STRING),
                                    'id'      => 'description',
                                    'type'    => 'wysiwyg',
                                    'options' => [
                                        'textarea_rows' => 3,
                                        'media_buttons' => false,
                                        'teeny'         => true,
                                    ],
                                    'columns' => 12,
                                ],
                            ],
                            'columns' => 12,
                            'tab'     => 'vision',
                        ],
                        [
                            'name'   => __('Member', LANG_STRING),
                            'id'     => 'member',
                            'type'   => 'group',
                            'fields' => [
                                [
                                    'name'    => __('Title', LANG_STRING),
                                    'id'      => 'title',
                                    'type'    => 'textarea',
                                    'rows'    => 2,
                                    'columns' => 6,
                                ],
                                [
                                    'name'          => __('Items', LANG_STRING),
                                    'id'            => 'item',
                                    'type'          => 'group',
                                    'clone'         => true,
                                    'sort_clone'    => true,
                                    'collapsible'   => true,
                                    'default_state' => 'collapsed',
                                    'group_title'   => '{#}- {title}',
                                    'fields'        => [
                                        [
                                            'name'    => __('Title', LANG_STRING),
                                            'id'      => 'title',
                                            'type'    => 'text',
                                            'columns' => 4,
                                        ],
                                        [
                                            'name'    => __('Subtitle', LANG_STRING),
                                            'id'      => 'subtitle',
                                            'type'    => 'text',
                                            'columns' => 4,
                                        ],
                                        [
                                            'name'    => __('Image', LANG_STRING),
                                            'id'      => 'image',
                                            'type'    => 'single_image',
                                            'columns' => 4,
                                        ],
                                    ],
                                    'columns' => 12,
                                ],
                            ],
                            'columns' => 12,
                            'tab'     => 'member',
                        ],
                        [
                            'name'   => __('Service', LANG_STRING),
                            'id'     => 'service',
                            'type'   => 'group',
                            'fields' => [
                                [
                                    'name'    => __('Description', LANG_STRING),
                                    'id'      => 'description',
                                    'type'    => 'wysiwyg',
                                    'options' => [
                                        'textarea_rows' => 3,
                                        'media_buttons' => false,
                                        'teeny'         => true,
                                    ],
                                    'columns' => 12,
                                ],
                            ],
                            'columns' => 12,
                            'tab'     => 'service',
                        ],
                        [
                            'name'   => __('Certificate', LANG_STRING),
                            'id'     => 'certificate',
                            'type'   => 'group',
                            'fields' => [
                                [
                                    'name'    => __('Title', LANG_STRING),
                                    'id'      => 'title',
                                    'type'    => 'textarea',
                                    'rows'    => 2,
                                    'columns' => 6,
                                ],
                                [
                                    'name'          => __('Items', LANG_STRING),
                                    'id'            => 'item',
                                    'type'          => 'group',
                                    'clone'         => true,
                                    'sort_clone'    => true,
                                    'collapsible'   => true,
                                    'default_state' => 'collapsed',
                                    'group_title'   => '{#}- {title}',
                                    'fields'        => [
                                        [
                                            'name'    => __('Title', LANG_STRING),
                                            'id'      => 'title',
                                            'type'    => 'text',
                                            'columns' => 4,
                                        ],
                                        [
                                            'name'    => __('Link', LANG_STRING),
                                            'id'      => 'link',
                                            'type'    => 'url',
                                            'columns' => 4,
                                        ],
                                        [
                                            'name'    => __('Image', LANG_STRING),
                                            'id'      => 'image',
                                            'type'    => 'single_image',
                                            'columns' => 4,
                                        ],
                                    ],
                                    'columns' => 12,
                                ],
                            ],
                            'columns' => 12,
                            'tab'     => 'certificate',
                        ],
                        [
                            'name'   => __('CTA', LANG_STRING),
                            'id'     => 'cta',
                            'type'   => 'group',
                            'fields' => [
                                [
                                    'name'    => __('Section Title', LANG_STRING),
                                    'id'      => 'title',
                                    'type'    => 'textarea',
                                    'rows'    => 2,
                                    'columns' => 6,
                                ],
                                [
                                    'name'    => __('Section Subtitle', LANG_STRING),
                                    'id'      => 'subtitle',
                                    'type'    => 'textarea',
                                    'rows'    => 2,
                                    'columns' => 6,
                                ],
                                [
                                    'name'    => __('Button', LANG_STRING),
                                    'id'      => 'button',
                                    'type'    => 'group',
                                    'fields'  => [
                                        [
                                            'name'    => __('Title', LANG_STRING),
                                            'id'      => 'title',
                                            'type'    => 'text',
                                            'columns' => 6,
                                        ],
                                        [
                                            'name'    => __('Link', LANG_STRING),
                                            'id'      => 'link',
                                            'type'    => 'url',
                                            'columns' => 6,
                                        ],
                                    ],
                                    'columns' => 6,
                                ],
                            ],
                            'columns' => 12,
                            'tab'     => 'cta',
                        ],
                    ],
                    'columns' => 12,
                ],
            ]
        ];

        $meta_boxes[] = [
            'id'         => 'standard-contact-options',
            'title'      => __('Options', LANG_STRING),
            'post_types' => 'page',
            'include'    => [
                'template' => 'page-contact.php',
            ],
            'context'  => 'normal',
            'priority' => 'high',
            'autosave' => false,
            'tabs'     => [
                'form' => ['label' => __('Form', LANG_STRING)],
            ],
            'tab_style'   => 'default',
            'tab_wrapper' => true,
            'fields'      => [
                [
                    'name'   => __('Page', LANG_STRING),
                    'id'     => 'page',
                    'type'   => 'group',
                    'fields' => [
                        [
                            'name'   => __('Form', LANG_STRING),
                            'id'     => 'form',
                            'type'   => 'group',
                            'fields' => [
                                [
                                    'name'    => __('Title', LANG_STRING),
                                    'id'      => 'title',
                                    'type'    => 'textarea',
                                    'rows'    => 2,
                                    'columns' => 6,
                                ],
                            ],
                            'columns' => 12,
                            'tab'     => 'form',
                        ],
                    ],
                    'columns' => 12,
                ],
            ]
        ];

        $meta_boxes[] = [
            'id'         => 'standard-faq-options',
            'title'      => __('Options', LANG_STRING),
            'post_types' => 'page',
            'include'    => [
                'template' => 'page-faq.php',
            ],
            'context'  => 'normal',
            'priority' => 'high',
            'autosave' => false,
            'tabs'     => [
                'faq' => ['label' => __('FAQ', LANG_STRING)],
                'cta' => ['label' => __('CTA', LANG_STRING)],
            ],
            'tab_style'   => 'default',
            'tab_wrapper' => true,
            'fields'      => [
                [
                    'name'   => __('Page', LANG_STRING),
                    'id'     => 'page',
                    'type'   => 'group',
                    'fields' => [
                        [
                            'name'              => __('Group', LANG_STRING),
                            'id'                => 'group',
                            'type'              => 'group',
                            'clone'             => true,
                            'sort_clone'        => true,
                            'clone_as_multiple' => true,
                            'add_button'        => __('+ Add', LANG_STRING) . ' ' . __('Group', LANG_STRING),
                            'collapsible'       => true,
                            'default_state'     => 'collapsed',
                            'group_title'       => '{#}- {category}',
                            'fields'            => [
                                [
                                    'name'    => __('Category', LANG_STRING),
                                    'id'      => 'category',
                                    'type'    => 'text',
                                    'columns' => 12,
                                ],
                                [
                                    'name'          => __('Items', LANG_STRING),
                                    'id'            => 'item',
                                    'type'          => 'group',
                                    'clone'         => true,
                                    'sort_clone'    => true,
                                    'collapsible'   => true,
                                    'default_state' => 'collapsed',
                                    'group_title'   => '{#}- {title}',
                                    'fields'        => [
                                        [
                                            'name'    => __('Title', LANG_STRING),
                                            'id'      => 'title',
                                            'type'    => 'textarea',
                                            'rows'    => 2,
                                            'columns' => 12,
                                        ],
                                        [
                                            'name'    => __('Description', LANG_STRING),
                                            'id'      => 'description',
                                            'type'    => 'wysiwyg',
                                            'options' => [
                                                'textarea_rows' => 3,
                                                'media_buttons' => false,
                                                'teeny'         => true,
                                            ],
                                            'columns' => 12,
                                        ],
                                    ],
                                    'columns' => 12,
                                ],
                            ],
                            'columns' => 12,
                            'tab'     => 'faq',
                        ],
                        [
                            'name'   => __('CTA', LANG_STRING),
                            'id'     => 'cta',
                            'type'   => 'group',
                            'fields' => [
                                [
                                    'name'    => __('Title', LANG_STRING),
                                    'id'      => 'title',
                                    'type'    => 'textarea',
                                    'rows'    => 2,
                                    'columns' => 6,
                                ],
                                [
                                    'name'    => __('Subtitle', LANG_STRING),
                                    'id'      => 'subtitle',
                                    'type'    => 'textarea',
                                    'rows'    => 2,
                                    'columns' => 6,
                                ],
                            ],
                            'columns' => 12,
                            'tab'     => 'cta',
                        ],
                    ],
                    'columns' => 12,
                ],
            ]
        ];

        $meta_boxes[] = [
            'id'         => 'standard-collection-options',
            'title'      => __('Options', LANG_STRING),
            'post_types' => 'page',
            'include'    => [
                'template' => 'page-collection.php',
            ],
            'context'  => 'normal',
            'priority' => 'high',
            'autosave' => false,
            'tabs'     => [
                'intro'      => ['label' => __('Intro', LANG_STRING)],
                'collection' => ['label' => __('Collection', LANG_STRING)],
            ],
            'tab_style'   => 'default',
            'tab_wrapper' => true,
            'fields'      => [
                [
                    'name'   => __('Page', LANG_STRING),
                    'id'     => 'page',
                    'type'   => 'group',
                    'fields' => [
                        [
                            'name'   => __('Intro', LANG_STRING),
                            'id'     => 'intro',
                            'type'   => 'group',
                            'fields' => [
                                [
                                    'name'    => __('Description', LANG_STRING),
                                    'id'      => 'description',
                                    'type'    => 'wysiwyg',
                                    'options' => [
                                        'textarea_rows' => 10,
                                        'media_buttons' => true,
                                        'teeny'         => true,
                                    ],
                                    'columns' => 12,
                                ],
                            ],
                            'columns' => 12,
                            'tab'     => 'intro',
                        ],
                        [
                            'name'   => __('Collection', LANG_STRING),
                            'id'     => 'collection',
                            'type'   => 'group',
                            'fields' => [
                                [
                                    'name'    => __('Section Title', LANG_STRING),
                                    'id'      => 'title',
                                    'type'    => 'textarea',
                                    'rows'    => 2,
                                    'columns' => 4,
                                ],
                                [
                                    'name'    => __('Section Subtitle', LANG_STRING),
                                    'id'      => 'subtitle',
                                    'type'    => 'textarea',
                                    'rows'    => 2,
                                    'columns' => 4,
                                ],
                                [
                                    'name'    => __('Image', LANG_STRING),
                                    'id'      => 'image',
                                    'type'    => 'image_advanced',
                                    'columns' => 4,
                                ],
                                [
                                    'name'    => __('Description', LANG_STRING),
                                    'id'      => 'description',
                                    'type'    => 'wysiwyg',
                                    'options' => [
                                        'textarea_rows' => 3,
                                        'media_buttons' => false,
                                        'teeny'         => true,
                                    ],
                                    'columns' => 12,
                                ],
                                [
                                    'name'          => __('Items', LANG_STRING),
                                    'id'            => 'item',
                                    'type'          => 'group',
                                    'clone'         => true,
                                    'sort_clone'    => true,
                                    'collapsible'   => true,
                                    'default_state' => 'collapsed',
                                    'group_title'   => '{#}- {title}',
                                    'fields'        => [
                                        [
                                            'name'    => __('Size', LANG_STRING),
                                            'id'      => 'size',
                                            'type'    => 'text',
                                            'columns' => 4,
                                        ],
                                        [
                                            'name'    => __('Title', LANG_STRING),
                                            'id'      => 'title',
                                            'type'    => 'text',
                                            'columns' => 4,
                                        ],
                                        [
                                            'name'       => __('Taxonomy', LANG_STRING),
                                            'id'         => 'taxonomy',
                                            'type'       => 'taxonomy_advanced',
                                            'taxonomy'   => ['pa_color', 'pa_origin', 'pa_design', 'pa_material', 'pa_shape', 'pa_thickness', 'pa_feel', 'product_cat'],
                                            'field_type' => 'select_advanced',
                                            'ajax'       => true,
                                            'columns'    => 4,
                                        ],
                                        [
                                            'name'    => __('Image', LANG_STRING),
                                            'id'      => 'image',
                                            'type'    => 'single_image',
                                            'columns' => 4,
                                        ],
                                        [
                                            'name'    => __('Description', LANG_STRING),
                                            'id'      => 'description',
                                            'type'    => 'wysiwyg',
                                            'options' => [
                                                'textarea_rows' => 3,
                                                'media_buttons' => false,
                                                'teeny'         => true,
                                            ],
                                            'columns' => 8,
                                        ],
                                    ],
                                    'columns' => 12,
                                ],
                            ],
                            'columns' => 12,
                            'tab'     => 'collection',
                        ],
                    ],
                    'columns' => 12,
                ],
            ]
        ];

        return $meta_boxes;
    }
}
