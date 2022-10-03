<?php

/**
 * Plugin Name: Custom API
 * Plugin URI: http://14.5005360.xyz
 * Description: yitu's Custon API
 * Version: 1.0
 * Author: Art Vandelay
 * Author URI: http://www.yitu.xyz
 */
// add_action('rest_api_init', function () {
//     register_rest_route('yitu/v1', 'post', [
//         'methods' => WP_REST_Server::READABLE,
//         'callback' => 'myapi_post'
//     ]);


//     register_rest_route('yitu/v1', '/products/(?P<id>[\d]+)', [
//         'methods' => 'GET',
//         'callback' => 'myapi_productos'
//     ]);
// });

/**
 * Disable direct access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Slug_Custom_Route extends WP_REST_Controller
{

    /**
     * Register the routes for the objects of the controller.
     */

    public function __construct()
    {
        $this->namespace    = 'yitu/v1';
        $this->rest_base = 'products';
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    // public function register_routes(){
    //     register_rest_route($this->namespace,'/' . $this->rest_base,array(
    //         'methods' => 'GET',
    //         'callback' => array($this, 'myapi_post' )
    //     ));
    // }

    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->rest_base, array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array($this, 'myapi_post'),
                # 'permission_callback' => array( $this, 'get_items_permissions_check' ),
                'args'                => array(),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array($this, 'create_item'),
                #  'permission_callback' => array( $this, 'create_item_permissions_check' ),
                'args'                => $this->get_endpoint_args_for_item_schema(true),
            ),
        ));
        register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_items'),
                #  'permission_callback' => array( $this, 'get_item_permissions_check' ),
                'args'                => array(
                    'context' => array(
                        'default' => 'view',
                    ),
                ),
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array($this, 'update_item'),
                'permission_callback' => array($this, 'update_item_permissions_check'),
                'args'                => $this->get_endpoint_args_for_item_schema(false),
            ),
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array($this, 'delete_item'),
                'permission_callback' => array($this, 'delete_item_permissions_check'),
                'args'                => array(
                    'force' => array(
                        'default' => false,
                    ),
                ),
            ),
        ));
        register_rest_route($this->namespace, '/' . $this->rest_base . '/schema', array(
            'methods'  => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_public_item_schema'),
        ));
    }


    public function myapi_post()
    {
        return 'i am working';
    }


    public function get_items($request)
    {
        // In practice this function would fetch the desired data. Here we are just making stuff up.
        $products = array(
            '1' => 'I am product 1',
            '2' => 'I am product 2',
            '3' => 'I am product 3',
        );

        // Here we are grabbing the 'id' path variable from the $request object. WP_REST_Request implements ArrayAccess, which allows us to grab properties as though it is an array.
        $id = (string) $request['id'];


        if (isset($products[$id])) {
            // Grab the product.
            $product = $products[$id];

            // Return the product as a response.
            return rest_ensure_response($product);
        } else {
            // Return a WP_Error because the request product was not found. In this case we return a 404 because the main resource was not found.
            return new WP_Error('rest_product_invalid', esc_html__('The product does not exist.', 'my-text-domain'), array('status' => 404));
        }

        // If the code somehow executes to here something bad happened return a 500.
        return new WP_Error('rest_api_sad', esc_html__('Something went horribly wrong.', 'my-text-domain'), array('status' => 500));
    }
}


if (is_plugin_active('myapi/myapi.php') ) {
	yitu_rest_api_v1_load_plugin('Slug_Custom_Route');
}


function yitu_rest_api_v1_load_plugin($class_name){
    global $yitu_rest_api_v1_plgins;
    if (!isset ($yitu_rest_api_v1_plgins)){
        $yitu_rest_api_v1_plgins = array();
        $_GLOBALS['yitu_rest_api_v1_plugins'] =array();
    }

    if (!isset($yitu_rest_api_v1_plgins[$class_name])){
        $yitu_rest_api_v1_plgins[$class_name]= new $class_name();
    }
}