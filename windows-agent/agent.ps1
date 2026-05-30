[CmdletBinding()]
param(
    [string]$ConfigPath = (Join-Path $PSScriptRoot 'config.json'),
    [string]$RuntimePath,
    [switch]$DryRun
)

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version 2.0

function Write-AgentInfo {
    param([string]$Message)

    $timestamp = (Get-Date).ToString('s')
    Write-Host "[$timestamp] $Message"
}

function Read-AgentConfig {
    param([string]$Path)

    if (-not (Test-Path -LiteralPath $Path)) {
        throw "Config file not found: $Path"
    }

    $rawConfig = Get-Content -LiteralPath $Path -Raw
    if ([string]::IsNullOrWhiteSpace($rawConfig)) {
        throw "Config file is empty: $Path"
    }

    return $rawConfig | ConvertFrom-Json
}

function Get-ConfigValue {
    param(
        [object]$Config,
        [string]$Name,
        [object]$DefaultValue = $null
    )

    if ($Config.PSObject.Properties.Name -contains $Name) {
        $value = $Config.$Name
        if ($null -ne $value -and -not [string]::IsNullOrWhiteSpace([string]$value)) {
            return $value
        }
    }

    return $DefaultValue
}

function Ensure-Directory {
    param([string]$Path)

    if (-not (Test-Path -LiteralPath $Path)) {
        New-Item -ItemType Directory -Path $Path -Force | Out-Null
    }
}

function Get-OrCreateAgentId {
    param([string]$Path)

    if (Test-Path -LiteralPath $Path) {
        $existing = (Get-Content -LiteralPath $Path -Raw).Trim()
        if (-not [string]::IsNullOrWhiteSpace($existing)) {
            $parsed = [guid]::Empty
            if ([guid]::TryParse($existing, [ref]$parsed)) {
                return $existing
            }

            throw "Existing agent_id is not a valid UUID: $Path"
        }
    }

    $agentId = [guid]::NewGuid().Guid
    Set-Content -LiteralPath $Path -Value $agentId -NoNewline -Encoding ASCII
    return $agentId
}

function Get-StoredToken {
    param([string]$Path)

    if (-not (Test-Path -LiteralPath $Path)) {
        return $null
    }

    $token = (Get-Content -LiteralPath $Path -Raw).Trim()
    if ([string]::IsNullOrWhiteSpace($token)) {
        return $null
    }

    return $token
}

function Save-AgentToken {
    param(
        [string]$Path,
        [string]$Token
    )

    if ([string]::IsNullOrWhiteSpace($Token)) {
        throw "Server did not return an agent token."
    }

    Set-Content -LiteralPath $Path -Value $Token -NoNewline -Encoding ASCII
}

function Get-HostName {
    if (-not [string]::IsNullOrWhiteSpace($env:COMPUTERNAME)) {
        return $env:COMPUTERNAME
    }

    return [System.Net.Dns]::GetHostName()
}

function Get-WindowsUser {
    try {
        return [System.Security.Principal.WindowsIdentity]::GetCurrent().Name
    } catch {
        if (-not [string]::IsNullOrWhiteSpace($env:USERDOMAIN) -and -not [string]::IsNullOrWhiteSpace($env:USERNAME)) {
            return "$env:USERDOMAIN\$env:USERNAME"
        }

        return $null
    }
}

function Get-IPv4Addresses {
    try {
        return Get-NetIPAddress -AddressFamily IPv4 |
            Where-Object {
                $_.IPAddress -and
                $_.IPAddress -notlike '127.*' -and
                $_.IPAddress -notlike '169.254.*' -and
                $_.PrefixOrigin -ne 'WellKnown'
            }
    } catch {
        return @()
    }
}

function Get-ZeroTierIPv4 {
    $addresses = @(Get-IPv4Addresses)

    try {
        $zeroTierAdapters = @(Get-NetAdapter |
            Where-Object {
                $_.Status -eq 'Up' -and
                (($_.Name -like '*ZeroTier*') -or ($_.InterfaceDescription -like '*ZeroTier*'))
            })

        foreach ($adapter in $zeroTierAdapters) {
            $match = $addresses | Where-Object { $_.InterfaceIndex -eq $adapter.ifIndex } | Select-Object -First 1
            if ($match) {
                return $match.IPAddress
            }
        }
    } catch {
        return $null
    }

    return $null
}

function Get-LocalIPv4 {
    $addresses = @(Get-IPv4Addresses)
    $zeroTierIp = Get-ZeroTierIPv4

    $local = $addresses |
        Where-Object { $_.IPAddress -ne $zeroTierIp } |
        Sort-Object -Property InterfaceMetric, InterfaceIndex |
        Select-Object -First 1

    if ($local) {
        return $local.IPAddress
    }

    if ($zeroTierIp) {
        return $zeroTierIp
    }

    return $null
}

function Get-OperatingSystemInfo {
    try {
        $os = Get-CimInstance -ClassName Win32_OperatingSystem
        if ($os.LastBootUpTime -is [datetime]) {
            $lastBoot = $os.LastBootUpTime
        } else {
            $lastBoot = [Management.ManagementDateTimeConverter]::ToDateTime($os.LastBootUpTime)
        }

        return [pscustomobject]@{
            Name = $os.Caption
            Version = $os.Version
            LastBoot = $lastBoot
        }
    } catch {
        return [pscustomobject]@{
            Name = $null
            Version = $null
            LastBoot = $null
        }
    }
}

function Get-RdpStatus {
    param([int]$Port)

    try {
        $service = Get-Service -Name TermService -ErrorAction Stop
        $serviceRunning = $service.Status -eq 'Running'
    } catch {
        return 'unknown'
    }

    if (-not $serviceRunning) {
        return 'unavailable'
    }

    try {
        $listener = Get-NetTCPConnection -LocalPort $Port -State Listen -ErrorAction Stop | Select-Object -First 1
        if ($listener) {
            return 'available'
        }
    } catch {
        return 'available'
    }

    return 'unavailable'
}

function ConvertTo-UsagePercent {
    param([object]$Value)

    if ($null -eq $Value) {
        return $null
    }

    try {
        $number = [double]$Value
        if ([double]::IsNaN($number) -or [double]::IsInfinity($number)) {
            return $null
        }

        if ($number -lt 0) {
            $number = 0
        }

        if ($number -gt 100) {
            $number = 100
        }

        return [math]::Round($number, 2)
    } catch {
        return $null
    }
}

function Get-CpuUsagePercent {
    try {
        $processor = Get-CimInstance `
            -ClassName Win32_PerfFormattedData_PerfOS_Processor `
            -Filter "Name='_Total'" `
            -ErrorAction Stop |
            Select-Object -First 1

        if ($processor -and $null -ne $processor.PercentProcessorTime) {
            return ConvertTo-UsagePercent -Value $processor.PercentProcessorTime
        }
    } catch {
        try {
            $counter = Get-Counter `
                -Counter '\Processor(_Total)\% Processor Time' `
                -SampleInterval 1 `
                -MaxSamples 1 `
                -ErrorAction Stop

            return ConvertTo-UsagePercent -Value $counter.CounterSamples[0].CookedValue
        } catch {
            return $null
        }
    }

    return $null
}

function Get-RamUsagePercent {
    try {
        $os = Get-CimInstance -ClassName Win32_OperatingSystem -ErrorAction Stop
        $totalKb = [double]$os.TotalVisibleMemorySize
        $freeKb = [double]$os.FreePhysicalMemory

        if ($totalKb -le 0) {
            return $null
        }

        return ConvertTo-UsagePercent -Value ((($totalKb - $freeKb) / $totalKb) * 100)
    } catch {
        return $null
    }
}

function Get-DiskUsagePercent {
    param([string]$DriveLetter)

    try {
        $driveId = $DriveLetter
        if ([string]::IsNullOrWhiteSpace($driveId)) {
            $driveId = $env:SystemDrive
        }

        if ([string]::IsNullOrWhiteSpace($driveId)) {
            $driveId = 'C:'
        }

        $driveId = $driveId.Trim().TrimEnd('\')
        if (-not $driveId.EndsWith(':')) {
            $driveId = "${driveId}:"
        }

        $disk = Get-CimInstance `
            -ClassName Win32_LogicalDisk `
            -Filter "DeviceID='$driveId'" `
            -ErrorAction Stop |
            Select-Object -First 1

        if (-not $disk -or $null -eq $disk.Size -or [double]$disk.Size -le 0) {
            return $null
        }

        $size = [double]$disk.Size
        $free = [double]$disk.FreeSpace

        return ConvertTo-UsagePercent -Value ((($size - $free) / $size) * 100)
    } catch {
        return $null
    }
}

function Test-TcpConnectivity {
    param(
        [string]$HostName,
        [int]$Port,
        [int]$TimeoutSeconds
    )

    $client = New-Object System.Net.Sockets.TcpClient
    $stopwatch = [System.Diagnostics.Stopwatch]::StartNew()
    $asyncResult = $null

    try {
        $asyncResult = $client.BeginConnect($HostName, $Port, $null, $null)
        $connected = $asyncResult.AsyncWaitHandle.WaitOne([TimeSpan]::FromSeconds($TimeoutSeconds))
        $stopwatch.Stop()

        if (-not $connected) {
            $client.Close()
            return [pscustomobject]@{
                TcpStatus = 'timeout'
                LatencyMs = $null
                FailureReason = "TCP connect timed out after ${TimeoutSeconds}s"
            }
        }

        try {
            $client.EndConnect($asyncResult)
        } catch {
            $message = $_.Exception.Message
            $status = 'failed'
            if ($message -match 'refused|actively refused') {
                $status = 'refused'
            }

            return [pscustomobject]@{
                TcpStatus = $status
                LatencyMs = $null
                FailureReason = $message
            }
        }

        return [pscustomobject]@{
            TcpStatus = 'connected'
            LatencyMs = [math]::Round($stopwatch.Elapsed.TotalMilliseconds, 0)
            FailureReason = $null
        }
    } catch {
        $stopwatch.Stop()
        return [pscustomobject]@{
            TcpStatus = 'failed'
            LatencyMs = $null
            FailureReason = $_.Exception.Message
        }
    } finally {
        if ($asyncResult -and $asyncResult.AsyncWaitHandle) {
            $asyncResult.AsyncWaitHandle.Close()
        }

        $client.Close()
    }
}

function Get-FirebirdConnectivityCheck {
    param([object]$Config)

    $enabled = [bool](Get-ConfigValue -Config $Config -Name 'firebird_check_enabled' -DefaultValue $false)
    if (-not $enabled) {
        return $null
    }

    $hostName = [string](Get-ConfigValue -Config $Config -Name 'firebird_host')
    if ([string]::IsNullOrWhiteSpace($hostName)) {
        return $null
    }

    $port = [int](Get-ConfigValue -Config $Config -Name 'firebird_port' -DefaultValue 3051)
    $timeoutSeconds = [int](Get-ConfigValue -Config $Config -Name 'firebird_timeout_seconds' -DefaultValue 3)
    if ($timeoutSeconds -lt 1) {
        $timeoutSeconds = 1
    }

    $result = Test-TcpConnectivity -HostName $hostName -Port $port -TimeoutSeconds $timeoutSeconds
    $overallStatus = 'unknown'
    if ($result.TcpStatus -eq 'connected') {
        $overallStatus = 'normal'
    } elseif ($result.TcpStatus -in @('timeout', 'refused', 'failed')) {
        $overallStatus = 'error'
    }

    return [ordered]@{
        target_type = 'firebird'
        target_host = $hostName
        target_port = $port
        ping_status = 'unknown'
        tcp_status = $result.TcpStatus
        tcp_latency_ms = $result.LatencyMs
        latency_ms = $result.LatencyMs
        failure_reason = $result.FailureReason
        status = $overallStatus
        checked_at = (Get-Date).ToUniversalTime().ToString('o')
    }
}

function Get-ProcessOwnerSafe {
    param([object]$Process)

    try {
        $owner = Invoke-CimMethod -InputObject $Process -MethodName GetOwner -ErrorAction Stop
        if ($owner.ReturnValue -eq 0 -and -not [string]::IsNullOrWhiteSpace($owner.User)) {
            if (-not [string]::IsNullOrWhiteSpace($owner.Domain)) {
                return "$($owner.Domain)\$($owner.User)"
            }

            return $owner.User
        }
    } catch {
        return $null
    }

    return $null
}

function Get-AccurateProcessCheck {
    param([object]$Config)

    $enabled = [bool](Get-ConfigValue -Config $Config -Name 'accurate_process_check_enabled' -DefaultValue $false)
    if (-not $enabled) {
        return $null
    }

    $processName = [string](Get-ConfigValue -Config $Config -Name 'accurate_process_name' -DefaultValue 'accurate.exe')
    if ([string]::IsNullOrWhiteSpace($processName)) {
        return $null
    }

    try {
        $process = Get-CimInstance -ClassName Win32_Process -ErrorAction Stop |
            Where-Object { $_.Name -ieq $processName } |
            Sort-Object -Property CreationDate -Descending |
            Select-Object -First 1
    } catch {
        $process = $null
    }

    if (-not $process) {
        return [ordered]@{
            process_name = $processName
            process_status = 'not_running'
            process_pid = $null
            process_owner = $null
            process_path = $null
            process_started_at = $null
            checked_at = (Get-Date).ToUniversalTime().ToString('o')
        }
    }

    $startedAt = $null
    try {
        if ($process.CreationDate) {
            $startedAt = ([Management.ManagementDateTimeConverter]::ToDateTime($process.CreationDate)).ToUniversalTime().ToString('o')
        }
    } catch {
        $startedAt = $null
    }

    return [ordered]@{
        process_name = $process.Name
        process_status = 'running'
        process_pid = $process.ProcessId
        process_owner = Get-ProcessOwnerSafe -Process $process
        process_path = $process.ExecutablePath
        process_started_at = $startedAt
        checked_at = (Get-Date).ToUniversalTime().ToString('o')
    }
}

function New-AgentPayload {
    param(
        [string]$AgentId,
        [object]$Config
    )

    $osInfo = Get-OperatingSystemInfo
    $lastBoot = $osInfo.LastBoot
    $uptimeSeconds = $null
    $lastBootAt = $null

    if ($lastBoot) {
        $uptimeSeconds = [int64]((Get-Date) - $lastBoot).TotalSeconds
        $lastBootAt = $lastBoot.ToUniversalTime().ToString('o')
    }

    $rdpPort = [int](Get-ConfigValue -Config $Config -Name 'rdp_port' -DefaultValue 3389)
    $monitoredDrive = [string](Get-ConfigValue -Config $Config -Name 'monitored_drive' -DefaultValue $env:SystemDrive)

    $payload = [ordered]@{
        agent_id = $AgentId
        hostname = Get-HostName
        windows_user = Get-WindowsUser
        ip_local = Get-LocalIPv4
        zerotier_ip = Get-ZeroTierIPv4
        os_name = $osInfo.Name
        os_version = $osInfo.Version
        cpu_usage_percent = Get-CpuUsagePercent
        ram_usage_percent = Get-RamUsagePercent
        disk_usage_percent = Get-DiskUsagePercent -DriveLetter $monitoredDrive
        uptime_seconds = $uptimeSeconds
        last_boot_at = $lastBootAt
        rdp_status = Get-RdpStatus -Port $rdpPort
        agent_version = [string](Get-ConfigValue -Config $Config -Name 'agent_version' -DefaultValue '1.0.0')
    }

    $firebirdCheck = Get-FirebirdConnectivityCheck -Config $Config
    if ($null -ne $firebirdCheck) {
        $payload['firebird_check'] = $firebirdCheck
    }

    $accurateProcess = Get-AccurateProcessCheck -Config $Config
    if ($null -ne $accurateProcess) {
        $payload['accurate_process'] = $accurateProcess
    }

    return $payload
}

function ConvertTo-AgentJson {
    param([object]$Payload)

    return $Payload | ConvertTo-Json -Depth 8
}

function ConvertTo-SyslogValue {
    param([object]$Value)

    if ($null -eq $Value) {
        return $null
    }

    if ($Value -is [bool]) {
        return ([string]$Value).ToLowerInvariant()
    }

    if ($Value -is [datetime]) {
        return $Value.ToUniversalTime().ToString('o')
    }

    $stringValue = [string]$Value
    if ([string]::IsNullOrWhiteSpace($stringValue)) {
        return $null
    }

    if ($stringValue -match '[\s"=]') {
        $escaped = $stringValue.Replace('\', '\\').Replace('"', '\"')
        return '"' + $escaped + '"'
    }

    return $stringValue
}

function ConvertTo-KeyValueLine {
    param([System.Collections.IDictionary]$Fields)

    $parts = New-Object System.Collections.Generic.List[string]
    $orderedKeys = New-Object System.Collections.Generic.List[string]

    foreach ($preferredKey in @('event_type', 'agent_id', 'hostname')) {
        if ($Fields.Contains($preferredKey)) {
            $orderedKeys.Add($preferredKey)
        }
    }

    foreach ($key in $Fields.Keys) {
        if (-not $orderedKeys.Contains([string]$key)) {
            $orderedKeys.Add([string]$key)
        }
    }

    foreach ($key in $orderedKeys) {
        $value = ConvertTo-SyslogValue -Value $Fields[$key]
        if ($null -ne $value) {
            $parts.Add("$key=$value")
        }
    }

    return ($parts -join ' ')
}

function New-SyslogMessage {
    param(
        [string]$Tag,
        [System.Collections.IDictionary]$Fields
    )

    return "${Tag}: $(ConvertTo-KeyValueLine -Fields $Fields)"
}

function Get-ObjectField {
    param(
        [object]$Object,
        [string]$Name
    )

    if ($null -eq $Object) {
        return $null
    }

    if ($Object -is [System.Collections.IDictionary] -and $Object.Contains($Name)) {
        return $Object[$Name]
    }

    if ($Object.PSObject.Properties.Name -contains $Name) {
        return $Object.$Name
    }

    return $null
}

function New-AgentSyslogMessages {
    param(
        [object]$Payload,
        [object]$Config
    )

    $appName = [string](Get-ConfigValue -Config $Config -Name 'syslog_app_name' -DefaultValue 'centralized-monitoring-agent')

    $deviceFields = [ordered]@{
        event_type = 'device_heartbeat'
        agent_id = $Payload.agent_id
        hostname = $Payload.hostname
        windows_user = $Payload.windows_user
        ip_local = $Payload.ip_local
        ip_zerotier = $Payload.zerotier_ip
        rdp_status = $Payload.rdp_status
        agent_version = $Payload.agent_version
        app_name = $appName
        status = 'online'
    }

    $perfFields = [ordered]@{
        event_type = 'performance_status'
        agent_id = $Payload.agent_id
        hostname = $Payload.hostname
        cpu_usage_percent = $Payload.cpu_usage_percent
        ram_usage_percent = $Payload.ram_usage_percent
        disk_usage_percent = $Payload.disk_usage_percent
        app_name = $appName
        status = 'reported'
    }

    $heartbeatFields = [ordered]@{
        event_type = 'heartbeat_status'
        agent_id = $Payload.agent_id
        hostname = $Payload.hostname
        uptime_seconds = $Payload.uptime_seconds
        last_boot_at = $Payload.last_boot_at
        app_name = $appName
        status = 'online'
    }

    $messages = New-Object System.Collections.Generic.List[string]
    $messages.Add((New-SyslogMessage -Tag 'device-monitor' -Fields $deviceFields))
    $messages.Add((New-SyslogMessage -Tag 'perf-monitor' -Fields $perfFields))
    $messages.Add((New-SyslogMessage -Tag 'heartbeat-monitor' -Fields $heartbeatFields))

    $firebirdCheck = Get-ObjectField -Object $Payload -Name 'firebird_check'
    if ($null -ne $firebirdCheck) {
        $networkFields = [ordered]@{
            event_type = 'firebird_connectivity'
            agent_id = $Payload.agent_id
            hostname = $Payload.hostname
            target_type = Get-ObjectField -Object $firebirdCheck -Name 'target_type'
            target_host = Get-ObjectField -Object $firebirdCheck -Name 'target_host'
            target_port = Get-ObjectField -Object $firebirdCheck -Name 'target_port'
            ping_status = Get-ObjectField -Object $firebirdCheck -Name 'ping_status'
            tcp_status = Get-ObjectField -Object $firebirdCheck -Name 'tcp_status'
            latency_ms = Get-ObjectField -Object $firebirdCheck -Name 'latency_ms'
            failure_reason = Get-ObjectField -Object $firebirdCheck -Name 'failure_reason'
            app_name = $appName
            status = Get-ObjectField -Object $firebirdCheck -Name 'status'
        }

        $messages.Add((New-SyslogMessage -Tag 'network-monitor' -Fields $networkFields))
    }

    $accurateProcess = Get-ObjectField -Object $Payload -Name 'accurate_process'
    if ($null -ne $accurateProcess) {
        $accurateFields = [ordered]@{
            event_type = 'accurate_process'
            agent_id = $Payload.agent_id
            hostname = $Payload.hostname
            windows_user = $Payload.windows_user
            process_name = Get-ObjectField -Object $accurateProcess -Name 'process_name'
            process_status = Get-ObjectField -Object $accurateProcess -Name 'process_status'
            process_pid = Get-ObjectField -Object $accurateProcess -Name 'process_pid'
            process_owner = Get-ObjectField -Object $accurateProcess -Name 'process_owner'
            process_path = Get-ObjectField -Object $accurateProcess -Name 'process_path'
            app_name = $appName
            status = if ((Get-ObjectField -Object $accurateProcess -Name 'process_status') -eq 'running') { 'normal' } else { 'warning' }
        }

        $messages.Add((New-SyslogMessage -Tag 'accurate-process-monitor' -Fields $accurateFields))
    }

    return $messages.ToArray()
}

function Send-SyslogUdp {
    param(
        [string]$Server,
        [int]$Port,
        [string]$Message
    )

    $udpClient = New-Object System.Net.Sockets.UdpClient

    try {
        $bytes = [System.Text.Encoding]::UTF8.GetBytes($Message)
        [void]$udpClient.Send($bytes, $bytes.Length, $Server, $Port)
    } finally {
        $udpClient.Close()
    }
}

function Send-AgentSyslogMessages {
    param(
        [object]$Config,
        [object]$Payload,
        [switch]$DryRun
    )

    $enabled = [bool](Get-ConfigValue -Config $Config -Name 'syslog_enabled' -DefaultValue $false)
    if (-not $enabled) {
        return
    }

    $server = [string](Get-ConfigValue -Config $Config -Name 'syslog_host')
    if ([string]::IsNullOrWhiteSpace($server)) {
        throw "Config value syslog_host is required when syslog_enabled=true."
    }

    $port = [int](Get-ConfigValue -Config $Config -Name 'syslog_port' -DefaultValue 5514)
    $protocol = ([string](Get-ConfigValue -Config $Config -Name 'syslog_protocol' -DefaultValue 'udp')).ToLowerInvariant()

    if ($protocol -ne 'udp') {
        throw "Only UDP syslog is supported by this PowerShell MVP. Set syslog_protocol to udp."
    }

    $messages = @(New-AgentSyslogMessages -Payload $Payload -Config $Config)

    if ($DryRun) {
        Write-AgentInfo "Syslog dry-run lines:"
        foreach ($message in $messages) {
            Write-Host $message
        }

        return
    }

    foreach ($message in $messages) {
        Send-SyslogUdp -Server $server -Port $port -Message $message
    }

    Write-AgentInfo "Syslog messages sent over UDP to ${server}:${port}."
}

function Invoke-AgentPost {
    param(
        [string]$Uri,
        [hashtable]$Headers,
        [object]$Payload,
        [int]$TimeoutSeconds
    )

    $body = ConvertTo-AgentJson -Payload $Payload

    return Invoke-RestMethod `
        -Method Post `
        -Uri $Uri `
        -Headers $Headers `
        -Body $body `
        -ContentType 'application/json' `
        -TimeoutSec $TimeoutSeconds
}

function Invoke-AgentGet {
    param(
        [string]$Uri,
        [hashtable]$Headers,
        [int]$TimeoutSeconds
    )

    return Invoke-RestMethod `
        -Method Get `
        -Uri $Uri `
        -Headers $Headers `
        -TimeoutSec $TimeoutSeconds
}

function New-RestartComment {
    param(
        [string]$Reason
    )

    $prefix = 'Restart requested by Centralized Log Monitoring Dashboard'
    if ([string]::IsNullOrWhiteSpace($Reason)) {
        return $prefix
    }

    $comment = "${prefix}: $Reason"
    if ($comment.Length -gt 512) {
        return $comment.Substring(0, 512)
    }

    return $comment
}

function Format-RestartCommandPreview {
    param(
        [int]$DelaySeconds,
        [string]$Comment
    )

    $escapedComment = $Comment.Replace('"', '\"')
    return "shutdown.exe /r /t $DelaySeconds /c `"$escapedComment`""
}

function Invoke-RestartClientCommand {
    param(
        [object]$Command,
        [object]$Config,
        [switch]$DryRun
    )

    $payload = Get-ObjectField -Object $Command -Name 'payload'
    $delay = Get-ObjectField -Object $payload -Name 'restart_delay_seconds'
    if ($null -eq $delay) {
        $delay = Get-ConfigValue -Config $Config -Name 'restart_delay_seconds' -DefaultValue 30
    }

    $delaySeconds = [int]$delay
    if ($delaySeconds -lt 0) {
        $delaySeconds = 0
    }

    $reason = [string](Get-ObjectField -Object $Command -Name 'reason')
    $comment = New-RestartComment -Reason $reason
    $preview = Format-RestartCommandPreview -DelaySeconds $delaySeconds -Comment $comment

    if ($DryRun) {
        Write-AgentInfo "Dry-run command $($Command.id): would execute $preview"
        return [pscustomobject]@{
            Status = 'succeeded'
            ResultMessage = "Dry run only. Would execute: $preview"
            ErrorMessage = $null
            ExecutedAt = (Get-Date).ToUniversalTime().ToString('o')
        }
    }

    try {
        Start-Process `
            -FilePath 'shutdown.exe' `
            -ArgumentList @('/r', '/t', [string]$delaySeconds, '/c', $comment) `
            -WindowStyle Hidden

        return [pscustomobject]@{
            Status = 'succeeded'
            ResultMessage = "Restart scheduled in $delaySeconds seconds."
            ErrorMessage = $null
            ExecutedAt = (Get-Date).ToUniversalTime().ToString('o')
        }
    } catch {
        return [pscustomobject]@{
            Status = 'failed'
            ResultMessage = $null
            ErrorMessage = $_.Exception.Message
            ExecutedAt = (Get-Date).ToUniversalTime().ToString('o')
        }
    }
}

function Send-CommandResult {
    param(
        [string]$ApiBaseUrl,
        [string]$AgentId,
        [hashtable]$Headers,
        [object]$Command,
        [object]$Result,
        [int]$TimeoutSeconds
    )

    $commandId = Get-ObjectField -Object $Command -Name 'id'
    $resultPayload = [ordered]@{
        agent_id = $AgentId
        status = $Result.Status
        result_message = $Result.ResultMessage
        error_message = $Result.ErrorMessage
        executed_at = $Result.ExecutedAt
    }

    Invoke-AgentPost `
        -Uri "$ApiBaseUrl/commands/$commandId/result" `
        -Headers $Headers `
        -Payload $resultPayload `
        -TimeoutSeconds $TimeoutSeconds | Out-Null
}

function Invoke-AgentCommandPolling {
    param(
        [object]$Config,
        [string]$ApiBaseUrl,
        [string]$AgentId,
        [hashtable]$Headers,
        [int]$TimeoutSeconds,
        [switch]$DryRun
    )

    $enabled = [bool](Get-ConfigValue -Config $Config -Name 'command_poll_enabled' -DefaultValue $false)
    if (-not $enabled) {
        return
    }

    $pollInterval = [int](Get-ConfigValue -Config $Config -Name 'command_poll_interval_seconds' -DefaultValue 30)
    if ($pollInterval -lt 1) {
        $pollInterval = 1
    }

    $agentIdParam = [System.Uri]::EscapeDataString($AgentId)
    $pendingUri = "$ApiBaseUrl/commands/pending?agent_id=$agentIdParam"

    if ($DryRun) {
        Write-AgentInfo "Command polling dry run enabled. Would GET $pendingUri every $pollInterval seconds when scheduled."
        return
    }

    try {
        $response = Invoke-AgentGet `
            -Uri $pendingUri `
            -Headers $Headers `
            -TimeoutSeconds $TimeoutSeconds
    } catch {
        Write-AgentInfo "Command polling failed: $($_.Exception.Message)"
        return
    }

    $commands = @($response.commands)
    if ($commands.Count -eq 0) {
        Write-AgentInfo "Command polling completed. No pending commands."
        return
    }

    foreach ($command in $commands) {
        $actionType = [string](Get-ObjectField -Object $command -Name 'action_type')
        if ($actionType -ne 'RESTART_CLIENT') {
            Write-AgentInfo "Ignoring unsupported command type: $actionType"
            continue
        }

        $result = Invoke-RestartClientCommand -Command $command -Config $Config

        try {
            Send-CommandResult `
                -ApiBaseUrl $ApiBaseUrl `
                -AgentId $AgentId `
                -Headers $Headers `
                -Command $command `
                -Result $result `
                -TimeoutSeconds $TimeoutSeconds

            Write-AgentInfo "Command $($command.id) result sent. Status: $($result.Status)"
        } catch {
            Write-AgentInfo "Failed to report command $($command.id) result: $($_.Exception.Message)"
        }
    }
}

try {
    [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

    $config = Read-AgentConfig -Path $ConfigPath
    if ([string]::IsNullOrWhiteSpace($RuntimePath)) {
        $RuntimePath = [string](Get-ConfigValue -Config $config -Name 'runtime_path' -DefaultValue 'C:\ProgramData\CentralizedLogMonitoring')
    }

    Ensure-Directory -Path $RuntimePath

    $agentIdPath = Join-Path $RuntimePath 'agent_id.txt'
    $tokenPath = Join-Path $RuntimePath 'agent-token.txt'
    $agentId = Get-OrCreateAgentId -Path $agentIdPath
    $token = Get-StoredToken -Path $tokenPath

    $apiBaseUrl = ([string](Get-ConfigValue -Config $config -Name 'api_base_url')).TrimEnd('/')
    if ([string]::IsNullOrWhiteSpace($apiBaseUrl)) {
        throw "Config value api_base_url is required."
    }

    $timeoutSeconds = [int](Get-ConfigValue -Config $config -Name 'request_timeout_seconds' -DefaultValue 15)
    $payload = New-AgentPayload -AgentId $agentId -Config $config

    if ($DryRun) {
        Write-AgentInfo "Dry run enabled. No HTTP requests will be sent."
        Write-AgentInfo "Runtime path: $RuntimePath"
        Write-AgentInfo "Token file present: $([bool]$token)"

        if (-not $token) {
            Write-AgentInfo "Register payload:"
            ConvertTo-AgentJson -Payload $payload | Write-Host
        }

        Write-AgentInfo "Heartbeat payload:"
        ConvertTo-AgentJson -Payload $payload | Write-Host

        Send-AgentSyslogMessages -Config $config -Payload ([pscustomobject]$payload) -DryRun
        Invoke-AgentCommandPolling `
            -Config $config `
            -ApiBaseUrl $apiBaseUrl `
            -AgentId $agentId `
            -Headers @{} `
            -TimeoutSeconds $timeoutSeconds `
            -DryRun

        Write-AgentInfo "Authorization: Bearer <redacted>"
        exit 0
    }

    if (-not $token) {
        Write-AgentInfo "No local token found. Registering agent."
        $registerResponse = Invoke-AgentPost `
            -Uri "$apiBaseUrl/register" `
            -Headers @{} `
            -Payload $payload `
            -TimeoutSeconds $timeoutSeconds

        $returnedToken = $registerResponse.credential.token
        if ([string]::IsNullOrWhiteSpace([string]$returnedToken)) {
            throw "Registration completed without a returned token. Token may already exist on server; regenerate credential or restore local token file."
        }

        Save-AgentToken -Path $tokenPath -Token ([string]$returnedToken)
        $token = [string]$returnedToken
        Write-AgentInfo "Agent registered. Token saved locally."
    }

    $headers = @{
        Authorization = "Bearer $token"
    }

    $heartbeatResponse = Invoke-AgentPost `
        -Uri "$apiBaseUrl/heartbeat" `
        -Headers $headers `
        -Payload $payload `
        -TimeoutSeconds $timeoutSeconds

    Write-AgentInfo "Heartbeat sent. Status: $($heartbeatResponse.status)"
    Send-AgentSyslogMessages -Config $config -Payload ([pscustomobject]$payload)
    Invoke-AgentCommandPolling `
        -Config $config `
        -ApiBaseUrl $apiBaseUrl `
        -AgentId $agentId `
        -Headers $headers `
        -TimeoutSeconds $timeoutSeconds
    exit 0
} catch {
    Write-Error $_.Exception.Message
    exit 1
}
