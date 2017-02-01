<?php

namespace App\Service;

class DirHelper {

    /** @var string[] */
    private static $dirListing = [];

    /**
     * Возвращает список поддиректорий внутри дирректории
     *
     * @param string $dirPath Путь дирректории
     *
     * @return string[]
     */
    public static function getDirListing($dirPath)
    {
        if (array_key_exists($dirPath, self::$dirListing)) {
            return self::$dirListing[ $dirPath ];
        }

        $dirListing = scandir($dirPath) ?: [];
        self::$dirListing[ $dirPath ] = $dirListing;

        return $dirListing;
    }


    /**
     * Возвращает кол-во строк в указанном файле
     *
     * @param string $filePath Путь до файла
     *
     * @return int
     */
    public static function getCountFileLines($filePath)
    {
        $file = fopen($filePath, 'r');
        $count = 0;
        while (fgets($file) !== false) {
            $count++;
        }
        fclose($file);

        return $count;
    }
} 