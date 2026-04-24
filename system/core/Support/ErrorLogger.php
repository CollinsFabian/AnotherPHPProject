<?php

declare(strict_types=1);

namespace Ziro\System\Support;

use Throwable;

class ErrorLogger
{
    public static function boot(): void
    {
        $logDirectory = base_path('system/storage/logs');
        if (!is_dir($logDirectory)) {
            mkdir($logDirectory, 0775, true);
        }

        $logFile = $logDirectory . DIRECTORY_SEPARATOR . 'php-error.log';

        ini_set('log_errors', '1');
        ini_set('display_errors', config('APP_DEBUG', 'false') === 'true' ? '1' : '0');
        ini_set('error_log', $logFile);

        set_exception_handler(static function (Throwable $exception) use ($logFile): void {
            self::writeException($exception, $logFile);
        });

        set_error_handler(static function (
            int $severity,
            string $message,
            string $file = '',
            int $line = 0
        ) use ($logFile): bool {
            if (!(error_reporting() & $severity)) {
                return false;
            }

            self::writeLine(sprintf(
                '[%s] PHP error [%s] %s in %s:%d',
                date('Y-m-d H:i:s'),
                self::severityLabel($severity),
                $message,
                $file,
                $line
            ), $logFile);

            return false;
        });

        register_shutdown_function(static function () use ($logFile): void {
            $lastError = error_get_last();
            if ($lastError === null) {
                return;
            }

            $fatalSeverities = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR];
            if (!in_array($lastError['type'] ?? 0, $fatalSeverities, true)) {
                return;
            }

            self::writeLine(sprintf(
                '[%s] Fatal shutdown [%s] %s in %s:%d',
                date('Y-m-d H:i:s'),
                self::severityLabel((int) $lastError['type']),
                $lastError['message'] ?? 'Unknown fatal error',
                $lastError['file'] ?? 'unknown',
                $lastError['line'] ?? 0
            ), $logFile);
        });
    }

    public static function logThrowable(Throwable $exception): void
    {
        $logFile = (string) ini_get('error_log');
        if ($logFile === '') {
            $logFile = base_path('storage/logs/php-error.log');
        }

        self::writeException($exception, $logFile);
    }

    protected static function writeException(Throwable $exception, string $logFile): void
    {
        self::writeLine(sprintf(
            "[%s] Uncaught %s: %s in %s:%d\nStack trace:\n%s",
            date('Y-m-d H:i:s'),
            $exception::class,
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        ), $logFile);
    }

    protected static function writeLine(string $message, string $logFile): void
    {
        error_log($message . PHP_EOL, 3, $logFile);
    }

    protected static function severityLabel(int $severity): string
    {
        return match ($severity) {
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
            default => 'E_UNKNOWN',
        };
    }
}
