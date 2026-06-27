<?php

namespace App\Services\Rsyslog;

use Carbon\CarbonImmutable;

class StructuredSyslogLineParser
{
    public function parse(string $line): ?array
    {
        $rawLine = rtrim($line, "\r\n");

        if ($rawLine === '') {
            return null;
        }

        $matched = preg_match(
            '/^(?<logged_at>\S+)\s+(?<hostname>\S+)\s+(?<source>[A-Za-z0-9_.-]+):\s*(?<message>.*)$/',
            $rawLine,
            $matches,
        );

        if (! $matched) {
            $matched = preg_match(
                '/^(?<source>[A-Za-z0-9_.-]+):\s*(?<message>.*)$/',
                $rawLine,
                $matches,
            );
        }

        if (! $matched) {
            return null;
        }

        $payload = $this->parseKeyValueMessage($matches['message'] ?? '');

        $loggedAt = null;
        if (! empty($matches['logged_at'])) {
            try {
                $loggedAt = CarbonImmutable::parse($matches['logged_at']);
            } catch (\Throwable) {
                $loggedAt = null;
            }
        }

        $hostname = $payload['hostname']
            ?? $payload['host']
            ?? $matches['hostname']
            ?? null;

        return [
            'raw_message' => $rawLine,
            'parsed_message' => $matches['message'] ?? '',
            'parsed_payload' => $payload,
            'logged_at' => $loggedAt,
            'hostname' => $hostname,
            'syslog_hostname' => $matches['hostname'] ?? null,
            'source' => rtrim($matches['source'], ':'),
            'event_type' => $payload['event_type'] ?? $payload['event'] ?? null,
            'agent_id' => $payload['agent_id'] ?? null,
            'ip_address' => $payload['ip_zerotier'] ?? $payload['zerotier_ip'] ?? $payload['ip_local'] ?? null,
        ];
    }

    public function parseKeyValueMessage(string $message): array
    {
        $payload = [];
        $length = strlen($message);
        $offset = 0;

        while ($offset < $length) {
            while ($offset < $length && ctype_space($message[$offset])) {
                $offset++;
            }

            if ($offset >= $length) {
                break;
            }

            if (! preg_match('/\G([A-Za-z0-9_.-]+)=/', $message, $keyMatch, 0, $offset)) {
                $nextSpace = strpos($message, ' ', $offset);
                if ($nextSpace === false) {
                    break;
                }

                $offset = $nextSpace + 1;
                continue;
            }

            $key = $keyMatch[1];
            $offset += strlen($keyMatch[0]);

            if ($offset < $length && $message[$offset] === '"') {
                [$value, $offset] = $this->readQuotedValue($message, $offset + 1);
            } else {
                $nextSpace = strpos($message, ' ', $offset);
                if ($nextSpace === false) {
                    $value = substr($message, $offset);
                    $offset = $length;
                } else {
                    $value = substr($message, $offset, $nextSpace - $offset);
                    $offset = $nextSpace + 1;
                }
            }

            $payload[$key] = $value;
        }

        return $payload;
    }

    private function readQuotedValue(string $message, int $offset): array
    {
        $value = '';
        $length = strlen($message);

        while ($offset < $length) {
            $char = $message[$offset];

            if ($char === '\\' && $offset + 1 < $length) {
                $nextChar = $message[$offset + 1];
                if ($nextChar === '"' || $nextChar === '\\') {
                    $value .= $nextChar;
                    $offset += 2;
                    continue;
                }
            }

            if ($char === '"') {
                return [$value, $offset + 1];
            }

            $value .= $char;
            $offset++;
        }

        return [$value, $offset];
    }
}
