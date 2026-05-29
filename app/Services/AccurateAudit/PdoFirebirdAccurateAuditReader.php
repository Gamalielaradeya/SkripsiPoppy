<?php

namespace App\Services\AccurateAudit;

use App\Models\AccurateAuditSource;
use App\Models\AccurateAuditSyncState;
use PDO;
use PDOException;

class PdoFirebirdAccurateAuditReader implements AccurateAuditReaderInterface
{
    public function fetchAuditRows(AccurateAuditSource $source, ?AccurateAuditSyncState $state, int $limit): array
    {
        if (! in_array('firebird', PDO::getAvailableDrivers(), true)) {
            throw new AccurateAuditReaderException('Firebird PDO driver is not installed. Enable pdo_firebird on the server.');
        }

        try {
            $pdo = $this->connect($source);
            [$sql, $bindings] = $this->buildAuditQuery($state, $limit);
            $statement = $pdo->prepare($sql);
            $statement->execute($bindings);

            return $statement->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (AccurateAuditReaderException $exception) {
            throw $exception;
        } catch (PDOException $exception) {
            throw new AccurateAuditReaderException('Firebird audit query failed. Check read-only credential, network, and AUDIT/USERS schema.');
        } catch (\Throwable $exception) {
            throw new AccurateAuditReaderException('Firebird audit reader failed safely.');
        }
    }

    /**
     * @return array{0: string, 1: array<int, mixed>}
     */
    public function buildAuditQuery(?AccurateAuditSyncState $state, int $limit): array
    {
        $limit = max(1, min($limit, 1000));
        $where = '';
        $bindings = [];
        $orderBy = 'a.AUDITID ASC';

        if ($state?->last_audit_id) {
            $where = 'WHERE a.AUDITID > ?';
            $bindings[] = $state->last_audit_id;
        } elseif ($state?->last_activity_time) {
            $where = 'WHERE a.MODIDATE > ?';
            $bindings[] = $state->last_activity_time->format('Y-m-d H:i:s');
            $orderBy = 'a.MODIDATE ASC, a.AUDITID ASC';
        }

        $sql = <<<SQL
SELECT FIRST {$limit}
    a.AUDITID,
    a.USERID,
    a.MODIDATE AS ACTIVITY_TIME,
    u.USERNAME AS ACCURATE_USERNAME,
    u.FULLNAME AS ACCURATE_FULLNAME,
    a.SOURCE,
    a.TRANSTYPE,
    a.TRANSDESCRIPTION,
    a.INVOICENO,
    a.COMP_NAME,
    a.IPADDRESS,
    a.APPVERSION,
    a.STATUS
FROM AUDIT a
LEFT JOIN USERS u
    ON a.USERID = u.USERID
{$where}
ORDER BY {$orderBy}
SQL;

        return [$sql, $bindings];
    }

    private function connect(AccurateAuditSource $source): PDO
    {
        $host = trim((string) $source->firebird_host);
        $port = (int) ($source->firebird_port ?: 3051);
        $database = trim((string) $source->database_path);
        $charset = config('monitoring.accurate_audit.firebird_charset', 'NONE') ?: 'NONE';

        if ($host === '' || $database === '') {
            throw new AccurateAuditReaderException('Firebird audit source is incomplete.');
        }

        $dsn = sprintf('firebird:dbname=%s/%d:%s;charset=%s', $host, $port, $database, $charset);
        $username = $source->username ?: config('monitoring.accurate_audit.firebird_username');
        $password = (string) config('monitoring.accurate_audit.firebird_password', '');

        return new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}
