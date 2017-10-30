<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 25/10/17
 * Time: 11:27
 */

namespace LM\WPPostLikeRestApi\Model;


class LMWallPostModel
{

    public function defineCustomPostWall() {

        $labels = array(
            'name'                  => _x( 'Wall', 'Post Type General Name', 'lm-sf-rest-api' ),
            'singular_name'         => _x( 'Wall Post', 'Post Type Singular Name', 'lm-sf-rest-api' ),
            'menu_name'             => __( 'Wall', 'lm-sf-rest-api' ),
            'name_admin_bar'        => __( 'Wall', 'lm-sf-rest-api' ),
            'archives'              => __( 'Wall Posts Archives', 'lm-sf-rest-api' ),
            'parent_item_colon'     => __( 'Parent Wall Post:', 'lm-sf-rest-api' ),
            'all_items'             => __( 'All Wall Posts', 'lm-sf-rest-api' ),
            'add_new_item'          => __( 'Add New Wall Post', 'lm-sf-rest-api' ),
            'add_new'               => __( 'Add New', 'lm-sf-rest-api' ),
            'new_item'              => __( 'New Wall Post', 'lm-sf-rest-api' ),
            'edit_item'             => __( 'Edit Wall Post', 'lm-sf-rest-api' ),
            'update_item'           => __( 'Update Wall Post', 'lm-sf-rest-api' ),
            'view_item'             => __( 'View Wall Posts', 'lm-sf-rest-api' ),
            'search_items'          => __( 'Search Wall Posts', 'lm-sf-rest-api' ),
            'not_found'             => __( 'Not found', 'lm-sf-rest-api' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'lm-sf-rest-api' ),
            'featured_image'        => __( 'Featured Image', 'lm-sf-rest-api' ),
            'set_featured_image'    => __( 'Set featured image', 'lm-sf-rest-api' ),
            'remove_featured_image' => __( 'Remove featured image', 'lm-sf-rest-api' ),
            'use_featured_image'    => __( 'Use as featured image', 'lm-sf-rest-api' ),
            'insert_into_item'      => __( 'Insert into item', 'lm-sf-rest-api' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'lm-sf-rest-api' ),
            'items_list'            => __( 'Items list', 'lm-sf-rest-api' ),
            'items_list_navigation' => __( 'Items list navigation', 'lm-sf-rest-api' ),
            'filter_items_list'     => __( 'Filter items list', 'lm-sf-rest-api' ),
        );
        $capabilities = array(
            'edit_post'             => 'edit_lm_wall',
            'read_post'             => 'read_lm_wall',
            'delete_posts'          => 'delete_lm_walls',
            'delete_post'          => 'delete_lm_wall',
            'delete_published_posts'=> 'delete_published_lm_walls',
            'edit_posts'            => 'edit_lm_walls',
            'edit_others_posts'     => 'edit_others_lm_walls',
            'publish_posts'         => 'publish_lm_wall',
            'read_private_posts'    => 'read_private_lm_wall',
        );
        $args = array(
            'label'                 => __( 'Wall', 'lm-sf-rest-api' ),
            'description'           => __( 'Wall Post', 'lm-sf-rest-api' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'post-formats'),
            'taxonomies'            => array(),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-format-chat',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capabilities'          => $capabilities,
            'rewrite'               => array( 'slug' => 'blog' ),
            'show_in_rest'          => true
        );

        register_post_type( 'lm_wall', $args );

    }

    public function defineCustomPostWallTaxonomy()
    {
        $labels = array(
            'name'              => __( 'Categoria', 'lm-sf-rest-api' ),
            'singular_name'     => __( 'Categoria', 'lm-sf-rest-api' ),
            'search_items'      => __( 'Cerca Categorie', 'lm-sf-rest-api' ),
            'all_items'         => __( 'Tutti le Categoria', 'lm-sf-rest-api' ),
            //'parent_item'       => __( 'Parent Therapeutic Area', 'lm-sf-rest-api' ),
            //'parent_item_colon' => __( 'Parent Therapeutic Area:', 'lm-sf-rest-api' ),
            'edit_item'         => __( 'Modifica Categoria', 'lm-sf-rest-api' ),
            'update_item'       => __( 'Aggiorna Categoria', 'lm-sf-rest-api' ),
            'add_new_item'      => __( 'Nuova Categoria', 'lm-sf-rest-api' ),
            'new_item_name'     => __( 'Nuovo Nome Prodotto', 'lm-sf-rest-api' ),
            'menu_name'         => __( 'Categorie', 'lm-sf-rest-api' ),
            'popular_items'     => __( 'Categorie popolari', 'lm-sf-rest-api' ),
            'separate_items_with_commas' => __( 'Separa più categorie con una virgola', 'lm-sf-rest-api' ),
            'add_or_remove_items'        => __( 'Aggiungi o rimuovi una categoria', 'lm-sf-rest-api' ),
            'choose_from_most_used'      => __( 'Scegli tra le categorie più usate', 'lm-sf-rest-api' ),
            'not_found'                  => __( 'Nessuna Categoria trovato.', 'lm-sf-rest-api' ),
        );

        // register taxonomy
        register_taxonomy(
            'lm_wall_category',
            array( 'lm_wall'),
            array(
                'labels' => $labels,
                'rewrite' => array( 'slug' => 'wall-category'),
                'hierarchical' => false,
                'show_admin_column' => true
            )
        );
    }

    public function setCustomPostWallCapabilities()
    {
        $role = get_role( 'administrator' );

        $role->add_cap( 'edit_lm_wall' );
        $role->add_cap( 'read_lm_wall' );
        $role->add_cap( 'delete_lm_walls' );
        $role->add_cap( 'delete_lm_wall' );
        $role->add_cap( 'delete_published_lm_walls' );
        $role->add_cap( 'edit_lm_walls' );
        $role->add_cap( 'edit_others_lm_walls' );
        $role->add_cap( 'publish_lm_wall' );
        $role->add_cap( 'read_private_lm_wall' );

        $role = get_role( 'editor' );

        $role->add_cap( 'edit_lm_wall' );
        $role->add_cap( 'read_lm_wall' );
        $role->add_cap( 'delete_lm_walls' );
        $role->add_cap( 'delete_lm_wall ' );
        $role->add_cap( 'delete_published_lm_walls' );
        $role->add_cap( 'edit_lm_walls' );
        $role->add_cap( 'edit_others_lm_walls' );
        $role->add_cap( 'publish_lm_wall' );
        $role->add_cap( 'read_private_lm_wall' );
    }

}