<?php
namespace Ninja_KNP\API;


if (!class_exists('Ninja_KNP\API\Ninja_KNP_REST_Controller')) {
class Ninja_KNP_REST_Controller extends \WP_REST_Controller {
    protected $namespace = 'ninja-knp/v1';
    protected $rest_base = '';

    public function check_permission($request) {
        return current_user_can('manage_options');
    }

    protected function prepare_response($data, $request) {
        return rest_ensure_response($data);
    }
}



}
