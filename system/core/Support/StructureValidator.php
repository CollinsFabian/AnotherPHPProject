<?php

declare(strict_types=1);

namespace Ziro\System\Support;

class StructureValidator
{
    public static function validate(): array
    {
        $rules = require base_path('system/core/Contracts/structure.rules.php');
        $failures = [];

        foreach ($rules['required_directories'] ?? [] as $directory) {
            if (!is_dir(base_path($directory))) {
                $failures[] = "Missing directory: {$directory}";
            }
        }

        foreach ($rules['required_files'] ?? [] as $file) {
            if (!is_file(base_path($file))) {
                $failures[] = "Missing file: {$file}";
            }
        }

        foreach ($rules['forbidden_paths'] ?? [] as $path) {
            if (file_exists(base_path($path))) {
                $failures[] = "Forbidden path present: {$path}";
            }
        }

        foreach ($rules['forbidden_path_patterns'] ?? [] as $pattern) {
            $failures = array_merge($failures, self::validateForbiddenPathPattern($pattern));
        }

        foreach ($rules['sealed_directories'] ?? [] as $directory => $constraints) {
            $failures = array_merge($failures, self::validateSealedDirectory($directory, $constraints));
        }

        foreach ($rules['sealed_directory_patterns'] ?? [] as $directory => $constraints) {
            $failures = array_merge($failures, self::validateSealedDirectoryPatterns($directory, $constraints));
        }

        foreach ($rules['allowed_root_entries'] ?? [] as $allowedRootEntries) {
            $failures = array_merge($failures, self::validateRootEntries($allowedRootEntries));
        }

        if (($rules['allowed_root_entry_patterns'] ?? []) !== []) {
            $failures = array_merge(
                $failures,
                self::validateRootEntryPatterns($rules['allowed_root_entry_patterns'])
            );
        }

        return array_values(array_unique($failures));
    }

    public static function assertValid(string $context = 'runtime'): void
    {
        $failures = self::validate();
        if ($failures === []) {
            return;
        }

        $message = "Invalid project structure detected during {$context}:\n- " . implode("\n- ", $failures);
        throw new \RuntimeException($message);
    }

    protected static function validateSealedDirectory(string $directory, array $constraints): array
    {
        $path = base_path($directory);
        if (!is_dir($path)) {
            return [];
        }

        $allowedFiles = $constraints['files'] ?? [];
        $allowedDirectories = $constraints['directories'] ?? [];
        $failures = [];

        foreach (scandir($path) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $entryPath = $path . DIRECTORY_SEPARATOR . $entry;
            if (is_file($entryPath) && !in_array($entry, $allowedFiles, true)) {
                $failures[] = "Unexpected file in {$directory}: {$entry}";
                continue;
            }

            if (is_dir($entryPath) && !in_array($entry, $allowedDirectories, true)) {
                $failures[] = "Unexpected directory in {$directory}: {$entry}";
            }
        }

        return $failures;
    }

    protected static function validateSealedDirectoryPatterns(string $directory, array $constraints): array
    {
        $path = base_path($directory);
        if (!is_dir($path)) {
            return [];
        }

        $allowedFilePatterns = $constraints['files'] ?? [];
        $allowedDirectoryPatterns = $constraints['directories'] ?? [];
        $failures = [];

        foreach (scandir($path) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $entryPath = $path . DIRECTORY_SEPARATOR . $entry;

            if (is_file($entryPath) && !self::matchesAnyPattern($entry, $allowedFilePatterns)) {
                $failures[] = "Unexpected file in {$directory}: {$entry}";
                continue;
            }

            if (is_dir($entryPath) && !self::matchesAnyPattern($entry, $allowedDirectoryPatterns)) {
                $failures[] = "Unexpected directory in {$directory}: {$entry}";
            }
        }

        return $failures;
    }

    protected static function validateRootEntries(array $allowedRootEntries): array
    {
        $rules = require base_path('system/core/Contracts/structure.rules.php');
        $ignoredRootEntries = $rules['ignored_root_entries'] ?? [];
        $ignoredRootPatterns = $rules['ignored_root_patterns'] ?? [];
        $root = base_path();
        $failures = [];

        foreach (scandir($root) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            if (in_array($entry, $ignoredRootEntries, true)) {
                continue;
            }

            $isIgnoredByPattern = false;
            foreach ($ignoredRootPatterns as $pattern) {
                if (preg_match($pattern, $entry) === 1) {
                    $isIgnoredByPattern = true;
                    break;
                }
            }

            if ($isIgnoredByPattern) {
                continue;
            }

            if (!in_array($entry, $allowedRootEntries, true)) {
                $failures[] = "Unexpected root entry: {$entry}";
            }
        }

        return $failures;
    }

    protected static function validateRootEntryPatterns(array $allowedRootEntryPatterns): array
    {
        $rules = require base_path('system/core/Contracts/structure.rules.php');
        $ignoredRootEntries = $rules['ignored_root_entries'] ?? [];
        $ignoredRootPatterns = $rules['ignored_root_patterns'] ?? [];
        $root = base_path();
        $failures = [];

        foreach (scandir($root) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            if (in_array($entry, $ignoredRootEntries, true) || self::matchesAnyPattern($entry, $ignoredRootPatterns)) {
                continue;
            }

            if (!self::matchesAnyPattern($entry, $allowedRootEntryPatterns)) {
                $failures[] = "Unexpected root entry: {$entry}";
            }
        }

        return $failures;
    }

    protected static function validateForbiddenPathPattern(string $pattern): array
    {
        $failures = [];

        foreach (self::listRelativePaths(base_path()) as $relativePath) {
            if (preg_match($pattern, $relativePath) === 1) {
                $failures[] = "Forbidden path present: {$relativePath}";
            }
        }

        return $failures;
    }

    protected static function listRelativePaths(string $root): array
    {
        $paths = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $relativePath = str_replace('\\', '/', substr($item->getPathname(), strlen($root) + 1));
            $paths[] = $relativePath;
        }

        return $paths;
    }

    protected static function matchesAnyPattern(string $value, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value) === 1) {
                return true;
            }
        }

        return false;
    }
}
