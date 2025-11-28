# PowerShell script to update all namespaces, class names, and identifiers to Ninja_KNP prefix

$baseDir = "d:\jobs\wp test email\wp-test-email-rebuilt\includes"

# Update API Endpoints
$file = "$baseDir\API\class-endpoints.php"
$content = Get-Content $file -Raw
$content = $content -replace "namespace NinjaTestEmail\\API;", "namespace Ninja_KNP\API;"
$content = $content -replace "use NinjaTestEmail\\Utils\\Singleton;", "use Ninja_KNP\Utils\Ninja_KNP_Singleton;"
$content = $content -replace "use NinjaTestEmail\\Core\\Loader;", "use Ninja_KNP\Core\Ninja_KNP_Loader;"
$content = $content -replace "use NinjaTestEmail\\Admin\\Admin_API;", "use Ninja_KNP\Admin\Ninja_KNP_Admin_API;"
$content = $content -replace "use NinjaTestEmail\\Frontend\\Frontend_API;", "use Ninja_KNP\Frontend\Ninja_KNP_Frontend_API;"
$content = $content -replace "class Endpoints \{", "class Ninja_KNP_Endpoints {"
$content = $content -replace "use Singleton;", "use Ninja_KNP_Singleton;"
$content = $content -replace "Loader \`$loader\)", "Ninja_KNP_Loader `$loader)"
$content = $content -replace "'NinjaTestEmail\\\\Admin\\\\Admin_API'", "'Ninja_KNP\\Admin\\Ninja_KNP_Admin_API'"
$content = $content -replace "'NinjaTestEmail\\\\Frontend\\\\Frontend_API'", "'Ninja_KNP\\Frontend\\Ninja_KNP_Frontend_API'"
$content = $content -replace "ninja-test-email/v1", "ninja-knp/v1"
$content | Set-Content $file

# Update API REST Controller
$file = "$baseDir\API\class-rest-controller.php"
$content = Get-Content $file -Raw
$content = $content -replace "namespace NinjaTestEmail\\API;", "namespace Ninja_KNP\API;"
$content = $content -replace "use NinjaTestEmail\\Utils\\Singleton;", "use Ninja_KNP\Utils\Ninja_KNP_Singleton;"
$content = $content -replace "use NinjaTestEmail\\Core\\Loader;", "use Ninja_KNP\Core\Ninja_KNP_Loader;"
$content = $content -replace "class REST_Controller \{", "class Ninja_KNP_REST_Controller {"
$content = $content -replace "use Singleton;", "use Ninja_KNP_Singleton;"
$content = $content -replace "Loader \`$loader\)", "Ninja_KNP_Loader `$loader)"
$content | Set-Content $file

# Update Email Logger
$file = "$baseDir\Core\class-email-logger.php"
$content = Get-Content $file -Raw
$content = $content -replace "namespace NinjaTestEmail\\Core;", "namespace Ninja_KNP\Core;"
$content = $content -replace "use NinjaTestEmail\\Utils\\LogManager;", "use Ninja_KNP\Utils\Ninja_KNP_Log_Manager;"
$content = $content -replace "class EmailLogger \{", "class Ninja_KNP_Email_Logger {"
$content = $content -replace "use Singleton;", "use Ninja_KNP_Singleton;"
$content = $content -replace "Loader \`$loader\)", "Ninja_KNP_Loader `$loader)"
$content = $content -replace "LogManager::", "Ninja_KNP_Log_Manager::"
$content | Set-Content $file

# Update Frontend
$file = "$baseDir\Frontend\class-frontend.php"
$content = Get-Content $file -Raw
$content = $content -replace "namespace NinjaTestEmail\\Frontend;", "namespace Ninja_KNP\Frontend;"
$content = $content -replace "use NinjaTestEmail\\Utils\\Singleton;", "use Ninja_KNP\Utils\Ninja_KNP_Singleton;"
$content = $content -replace "use NinjaTestEmail\\Core\\Loader;", "use Ninja_KNP\Core\Ninja_KNP_Loader;"
$content = $content -replace "class Frontend \{", "class Ninja_KNP_Frontend {"
$content = $content -replace "use Singleton;", "use Ninja_KNP_Singleton;"
$content = $content -replace "Loader \`$loader\)", "Ninja_KNP_Loader `$loader)"
$content | Set-Content $file

# Update Frontend API
$file = "$baseDir\Frontend\class-frontend-api.php"
$content = Get-Content $file -Raw
$content = $content -replace "namespace NinjaTestEmail\\Frontend;", "namespace Ninja_KNP\Frontend;"
$content = $content -replace "class Frontend_API \{", "class Ninja_KNP_Frontend_API {"
$content | Set-Content $file

# Update Helpers
$file = "$baseDir\Utils\class-helpers.php"
$content = Get-Content $file -Raw
$content = $content -replace "namespace NinjaTestEmail\\Utils;", "namespace Ninja_KNP\Utils;"
$content = $content -replace "class Helpers \{", "class Ninja_KNP_Helpers {"
$content = $content -replace "get_option\('ninja_test_email_options'", "get_option('ninja_knp_options'"
$content = $content -replace "update_option\('ninja_test_email_options'", "update_option('ninja_knp_options'"
$content | Set-Content $file

# Update Log Manager
$file = "$baseDir\Utils\class-log-manager.php"
$content = Get-Content $file -Raw
$content = $content -replace "namespace NinjaTestEmail\\Utils;", "namespace Ninja_KNP\Utils;"
$content = $content -replace "use NinjaTestEmail\\Core\\Loader;", "use Ninja_KNP\Core\Ninja_KNP_Loader;"
$content = $content -replace "class LogManager \{", "class Ninja_KNP_Log_Manager {"
$content = $content -replace "use Singleton;", "use Ninja_KNP_Singleton;"
$content = $content -replace "Loader \`$loader\)", "Ninja_KNP_Loader `$loader)"
$content = $content -replace "ninja_test_email_logs", "ninja_knp_logs"
$content | Set-Content $file

# Update Singleton Trait
$file = "$baseDir\Utils\trait-singleton.php"
$content = Get-Content $file -Raw
$content = $content -replace "namespace NinjaTestEmail\\Utils;", "namespace Ninja_KNP\Utils;"
$content = $content -replace "trait Singleton \{", "trait Ninja_KNP_Singleton {"
$content | Set-Content $file

Write-Host "All files updated successfully!" -ForegroundColor Green
