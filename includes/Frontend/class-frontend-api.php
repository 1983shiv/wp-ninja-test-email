<?php
namespace NinjaTestEmail\Frontend;

use NinjaTestEmail\Utils\Helpers;

class Frontend_API {
    public static function get_data($request) {
        return new \WP_REST_Response(array(
            'success' => true,
            'data'    => array(
                'message' => 'Hello from Ninja Email Test!',
            ),
        ), 200);
    }

    public static function submit_form($request) {
        $data = $request->get_json_params();

        return new \WP_REST_Response(array(
            'success' => true,
            'message' => 'Form submitted successfully',
            'data'    => $data,
        ), 200);
    }
}
