<?php

declare(strict_types=1);

namespace Sirian\YMLParser;

use Sirian\YMLParser\Builder\BuilderInterface;
use Sirian\YMLParser\Exception\YMLException;
use Sirian\YMLParser\Factory\Factory;
use Sirian\YMLParser\Factory\FactoryInterface;
use Sirian\YMLParser\Reader\Reader;

class Parser
{
    private FactoryInterface $factory;

    public function __construct(?FactoryInterface $factory = null)
    {
        $this->factory = $factory ?? new Factory();
    }

    /**
     * @throws YMLException
     */
    public function parse(string $file): BuilderInterface
    {
        if (!is_file($file) || !is_readable($file)) {
            throw new YMLException(sprintf('YML file is missing or not readable: %s', $file));
        }

        $reader = new Reader();

        $previousUseErrors = libxml_use_internal_errors(true);
        libxml_clear_errors();

        try {
            if (!$reader->open($file, LIBXML_NONET | LIBXML_COMPACT)) {
                throw new YMLException(sprintf('Cannot open YML file: %s', $file));
            }

            return $this->read($reader);
        } catch (\Throwable $e) {
            $reader->close();
            throw $e;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousUseErrors);
        }
    }

    /**
     * @throws YMLException
     */
    private function read(Reader $reader): BuilderInterface
    {
        if (!$reader->readUntil('/yml_catalog/shop')) {
            $reader->close();
            throw new YMLException('Invalid YML file: <shop> element not found');
        }

        if (!$reader->stepIn()) {
            $reader->close();

            return $this->factory->createBuilder('<shop></shop>', null);
        }

        $shopXML = '';

        do {
            if ('/yml_catalog/shop/offers' === $reader->getPath()) {
                if (!$reader->stepIn()) {
                    $reader->close();

                    return $this->factory->createBuilder('<shop>'.$shopXML.'</shop>', null);
                }

                return $this->factory->createBuilder('<shop>'.$shopXML.'</shop>', $reader);
            }

            $shopXML .= $reader->readOuterXml();
        } while ($reader->moveToNextSibling());

        $reader->close();

        return $this->factory->createBuilder('<shop>'.$shopXML.'</shop>', null);
    }
}
