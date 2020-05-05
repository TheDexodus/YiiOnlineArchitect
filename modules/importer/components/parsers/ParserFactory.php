<?php

declare(strict_types=1);

namespace importer\components\parsers;

/**
 * Class ParserFactory
 */
class ParserFactory
{
    /**
     * @param string $extension
     *
     * @return ModelParser|null
     */
    public static function getParser(string $extension): ?ModelParser
    {
        switch ($extension) {
            case 'txt':
                return new TXTParser();
            case 'csv':
                return new CSVParser();
            case 'xls':
                return new XLSParser();
        }

        return null;
    }
}
