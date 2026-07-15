<?php

declare(strict_types=1);

namespace Sirian\YMLParser\Reader;

class Reader
{
    private \XMLReader $xml;

    private string $path = '';

    private bool $opened = false;

    public function __construct()
    {
        $this->xml = new \XMLReader();
    }

    public function __destruct()
    {
        $this->close();
    }

    public function open(string $file, int $options = 0): bool
    {
        if (false === $this->xml->open($file, null, $options)) {
            return false;
        }

        $this->path = '';
        $this->opened = true;

        return true;
    }

    public function close(): void
    {
        if (!$this->opened) {
            return;
        }
        $this->xml->close();
        $this->opened = false;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Reads forward until the current element path matches $target.
     *
     * @phpstan-impure
     */
    public function readUntil(string $target): bool
    {
        while ($this->xml->read()) {
            if (\XMLReader::END_ELEMENT === $this->xml->nodeType) {
                $this->popPath();
                continue;
            }

            if (\XMLReader::ELEMENT !== $this->xml->nodeType || $this->xml->isEmptyElement) {
                continue;
            }

            $this->pushPath();

            if ($target === $this->getPath()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Descends into the current element and stops at its first child element.
     *
     * @phpstan-impure
     */
    public function stepIn(): bool
    {
        if ($this->xml->isEmptyElement) {
            return false;
        }
        while ($this->xml->read()) {
            if (\XMLReader::ELEMENT === $this->xml->nodeType) {
                $this->pushPath();

                return true;
            }
            if (\XMLReader::END_ELEMENT === $this->xml->nodeType) {
                $this->popPath();

                return false;
            }
        }

        return false;
    }

    /**
     * Advances to the next sibling element of the current one.
     *
     * @phpstan-impure
     */
    public function moveToNextSibling(): bool
    {
        $this->popPath();
        while ($this->xml->next()) {
            if (\XMLReader::ELEMENT === $this->xml->nodeType) {
                $this->pushPath();

                return true;
            }
            if (\XMLReader::END_ELEMENT === $this->xml->nodeType) {
                return false;
            }
        }

        return false;
    }

    public function readOuterXml(): string
    {
        return $this->xml->readOuterXml();
    }

    private function popPath(): void
    {
        $pos = strrpos($this->path, '/');
        if (false === $pos) {
            $this->path = '';
        } else {
            $this->path = substr($this->path, 0, $pos);
        }
    }

    private function pushPath(): void
    {
        $this->path .= '/'.$this->xml->name;
    }
}
