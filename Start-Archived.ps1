param([switch] $NoBrowser)

$ErrorActionPreference = 'Stop'
$php = 'C:\xampp\php8.5.12\php.exe'
$address = '127.0.0.1:8002'
$url = "http://$address/login"
$router = Join-Path $PSScriptRoot 'server.php'
$log = Join-Path $PSScriptRoot 'storage\logs\archived-server.log'

if (!(Test-Path -LiteralPath $php)) {
    throw "PHP 8.5.12 was not found at $php."
}

$listeners = netstat -ano -p TCP | Select-String '\s+(127\.0\.0\.1|0\.0\.0\.0):8002\s+0\.0\.0\.0:0\s+LISTENING\s+(\d+)'
foreach ($listener in $listeners) {
    $processId = [int] $listener.Matches[0].Groups[2].Value
    $process = Get-CimInstance Win32_Process -Filter "ProcessId = $processId"
    if ($process.ExecutablePath -ne $php -or !$process.CommandLine.Contains($router)) {
        throw "Port 8002 is already being used by another server."
    }
}

if (!$listeners) {
    $dumpDirectory = 'C:\xampp\mysql\bin'
    if (!$env:DB_DUMP_BINARY_PATH -and (Test-Path -LiteralPath "$dumpDirectory\mysqldump.exe")) {
        $env:DB_DUMP_BINARY_PATH = $dumpDirectory
    }
    $arguments = @('-S', $address, '-t', 'public', ('"{0}"' -f $router))
    Start-Process -FilePath $php -ArgumentList $arguments -WorkingDirectory $PSScriptRoot -WindowStyle Hidden -RedirectStandardError $log
}

& curl.exe --noproxy '*' --fail --silent --show-error --retry 4 --retry-connrefused --retry-delay 1 --max-time 8 -o NUL $url
if ($LASTEXITCODE -ne 0) {
    throw "The government archive login could not be reached. See $log."
}

if ($NoBrowser) {
    Write-Output $url
} else {
    Start-Process $url
}
