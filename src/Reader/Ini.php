<?php

namespace Borsch\Config\Reader;

use Borsch\Config\Exception\ReaderException;
use function in_array, is_file, is_readable, parse_ini_file, parse_ini_string, restore_error_handler, set_error_handler;

class Ini implements ReaderInterface
{

    private bool $process_sections = true;
    private int $scanner_mode = INI_SCANNER_TYPED;

    public function isProcessSections(): bool
    {
        return $this->process_sections;
    }

    public function setProcessSections(bool $process_sections): Ini
    {
        $this->process_sections = $process_sections;

        return $this;
    }

    public function getScannerMode(): int
    {
        return $this->scanner_mode;
    }

    /**
     * @throws ReaderException
     */
    public function setScannerMode(int $scanner_mode): Ini
    {
        if (!in_array($scanner_mode, [INI_SCANNER_NORMAL, INI_SCANNER_RAW, INI_SCANNER_TYPED], true)) {
            throw ReaderException::invalidArgument('scanner mode', 'INI');
        }

        $this->scanner_mode = $scanner_mode;

        return $this;
    }

    /**
     * @return array<string, mixed>
     * @throws ReaderException
     */
    public function fromFile(string $filename): array
    {
        if (!is_file($filename) || !is_readable($filename)) {
            throw ReaderException::fileDoesNotExistOrIsNotReadable($filename);
        }

        set_error_handler(
            static function ($error, $message = '') use ($filename) {
                restore_error_handler();
                throw ReaderException::errorReadingFile($filename, $message, 'INI');
            },
            E_WARNING
        );

        /** @var array<string, mixed> $ini */
        $ini = parse_ini_file($filename, $this->process_sections, $this->scanner_mode);

        restore_error_handler();

        return $ini ?: [];
    }

    /**
     * @return array<string, mixed>
     * @throws ReaderException
     */
    public function fromString(string $contents): array
    {
        if (empty($contents)) {
            return [];
        }

        set_error_handler(
            static function ($error, $message = '') {
                restore_error_handler();
                throw ReaderException::errorReadingString($message, 'INI');
            },
            E_WARNING
        );

        /** @var array<string, mixed> $ini */
        $ini = parse_ini_string($contents, $this->process_sections, $this->scanner_mode);

        restore_error_handler();

        return $ini ?: [];
    }
}
