<?php
// Test script to verify settings functionality
// This should be run in a WordPress environment

// Test 1: Check if default options are set
$options = get_option('ninja_knp_options', array());
echo "Current options: " . print_r($options, true) . "\n";

// Test 2: Test setting a value
$test_settings = array(
    'enabled' => true,
    'admin_capability' => 'manage_options',
    'test_field' => 'test_value'
);

$updated = update_option('ninja_knp_options', $test_settings);
echo "Update result: " . ($updated ? 'success' : 'failed') . "\n";

// Test 3: Verify the value was saved
$saved_options = get_option('ninja_knp_options', array());
echo "Saved options: " . print_r($saved_options, true) . "\n";

// Test 4: Test boolean handling
$bool_test = array(
    'enabled' => false,
    'admin_capability' => 'manage_options'
);

update_option('ninja_knp_options', $bool_test);
$bool_saved = get_option('ninja_knp_options', array());
echo "Boolean test - enabled value: " . (is_bool($bool_saved['enabled']) ? 'boolean' : gettype($bool_saved['enabled'])) . " = " . ($bool_saved['enabled'] ? 'true' : 'false') . "\n";

echo "Settings test completed.\n";
