$baseDir = 'd:\jobs\wp test email\wp-test-email-rebuilt'
$files = @(
    'includes\Admin\class-admin-api.php',
    'includes\Admin\views\admin-page.php',
    'includes\API\class-endpoints.php',
    'includes\API\class-rest-controller.php',
    'includes\Frontend\class-frontend-api.php',
    'includes\Frontend\views\shortcode.php',
    'includes\Utils\class-helpers.php',
    'includes\Utils\class-log-manager.php',
    'includes\Utils\trait-singleton.php'
)

foreach ($file in $files) {
    $fullPath = Join-Path $baseDir $file
    if (Test-Path $fullPath) {
        $content = [System.IO.File]::ReadAllText($fullPath)
        $content = $content.Replace("`r`n", "`n")
        $utf8NoBom = New-Object System.Text.UTF8Encoding $false
        [System.IO.File]::WriteAllText($fullPath, $content, $utf8NoBom)
        Write-Host "Fixed: $file"
    } else {
        Write-Host "Not found: $fullPath"
    }
}

Write-Host "`nAll files processed!"
