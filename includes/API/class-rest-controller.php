<?php
namespace NinjaTestEmail\API;

class Rest_Controller extends \WP_REST_Controller {
    protected $namespace = 'ninja-email-test/v1';
    protected $rest_base = '';

    public function check_permission($request) {
        return current_user_can('manage_options');
    }

    protected function prepare_response($data, $request) {
        return rest_ensure_response($data);
    }
}
