<?php

namespace App\Command;

use App\Service\DirHelper;

/**
 * Class UserTextUtilCountAverage
 * Для каждого пользователя посчитать среднее количество строк в его текстовых файлах
 * и вывести на экран вместе с именем пользователя.
 *
 * @package App\Command
 */
class UserTextUtilCountAverage implements UserTextUtilAction
{
    /** @var string Разделитель в файле csv */
    private $delimiter;

    /** @var string Путь до файла с людьми */
    private $filePeopleSource;

    /** @var string Путь до файла подробностями о людях */
    private $pathPeopleTextSource;

    /** @var string */
    private $output = '';

    public function __construct($delimiter, $filePeopleSource, $pathPeopleTextSource)
    {
        $this->delimiter = $delimiter;
        $this->filePeopleSource = $filePeopleSource;
        $this->pathPeopleTextSource = $pathPeopleTextSource;
    }

    public function __invoke()
    {
        $filePeople = fopen($this->filePeopleSource, 'r');
        while ($item = fgetcsv($filePeople, null, $this->delimiter)) {
            $personId = $item[0];
            $personName = $item[1];
            $averageLine = $this->getAverageTextLinesByPersonId($personId);

            $this->output .= "{$personName}: $averageLine" . PHP_EOL;
        }
        fclose($filePeople);

        return true;
    }

    /**
     * Возвращает среднее кол-во строк в файлах юзера
     *
     * @param $personId
     *
     * @return int
     */
    private function getAverageTextLinesByPersonId($personId)
    {
        $dirListing = DirHelper::getDirListing($this->pathPeopleTextSource);
        $personsFiles = array_filter(
            $dirListing,
            function ($dirName) use ($personId){
                return preg_match('/^' . $personId . '-\d{1,}/', $dirName);
            }
        );

        if(!$personsFiles){
            return 0;
        }

        $totalLines = 0;
        foreach ($personsFiles as $fileName) {
            $filePersonSource = $this->pathPeopleTextSource . $fileName;
            $totalLines += DirHelper::getCountFileLines($filePersonSource);
        }

        if($totalLines === 0){
            return 0;
        }
        $averageLines = round($totalLines / count($personsFiles));

        return $averageLines;
    }

    public function output()
    {
        return $this->output;
    }
} 