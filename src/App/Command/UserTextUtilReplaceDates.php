<?php

namespace App\Command;

use App\Service\DirHelper;

/**
 * Class UserTextUtilReplaceDates
 * Поместить тексты пользователей в папку ./output_texts, заменив в каждом тексте даты в
 * формате dd/mm/yy на даты в формате mm-dd-yyyy. Вывести на экран количество совершенных
 * для каждого пользователя замен вместе с именем пользователя.
 *
 * @package App\Command
 */
class UserTextUtilReplaceDates implements UserTextUtilAction
{
    /** @var string Разделитель в файле csv */
    private $delimiter;

    /** @var string Путь до файла с людьми */
    private $filePeopleSource;

    /** @var string Путь до файла подробностями о людях */
    private $pathPeopleTextSource;

    /** @var string Путь до файла с выводом результата */
    private $pathPeopleTextOutput;

    /** @var string */
    private $output = '';

    public function __construct($delimiter, $filePeopleSource, $pathPeopleTextSource, $pathPeopleTextOutput)
    {
        $this->delimiter = $delimiter;
        $this->filePeopleSource = $filePeopleSource;
        $this->pathPeopleTextSource = $pathPeopleTextSource;
        $this->pathPeopleTextOutput = $pathPeopleTextOutput;
    }

    public function __invoke()
    {
        $filePeople = fopen($this->filePeopleSource, 'r');
        if(mkdir($this->pathPeopleTextSource, 0600)){
            throw new \ErrorException('Could not create directory');
        }

        while ($item = fgetcsv($filePeople, null, $this->delimiter)) {
            $personId = $item[0];
            $personName = $item[1];
            $count = $this->replaceDatesByPersonId($personId);

            $this->output .= "{$personName}: {$count}" . PHP_EOL;
        }

        return true;
    }

    /**
     * Заменяет даты в текстовых файлах юзера, возвращает кол-во произведенных замен
     *
     * @param $personId
     *
     * @return int Кол-во произведенных замен
     */
    private function replaceDatesByPersonId($personId)
    {
        // todo: дублирование. Возможно стоит вынести в другой класс
        $dirListing = DirHelper::getDirListing($this->pathPeopleTextSource);
        $personsFiles = array_filter(
            $dirListing,
            function ($dirName) use ($personId){
                return preg_match('/^' . $personId . '-\d{1,}/', $dirName);
            }
        );

        if(!$personsFiles){
            return false;
        }

        $totalLines = 0;
        foreach ($personsFiles as $fileName) {
            $filePersonSource = $this->pathPeopleTextSource . $fileName;
            $filePersonOutput = $this->pathPeopleTextOutput . $fileName;
            $totalLines += $this->replaceDates($filePersonSource, $filePersonOutput);
        }

        return $totalLines;
    }

    /**
     * Заменяет даты в текстомов файле, возвращает кол-во произведенных замен
     *
     * @param string $filePathSource Путь до текстового файла
     * @param string $filePathDestination Путь для вывода результат
     *
     * @return int Кол-во произведенных замен
     */
    private function replaceDates($filePathSource, $filePathDestination)
    {
        $fp = fopen($filePathSource, 'r');
        $fpOutput = fopen($filePathDestination, 'w+');

        $count = 0;

        // todo: только для файлов с окончанием строк
        // todo: для больших файлов придется считывать ручками по небольшим кускам
        while (($line = fgets($fp)) !== false) {
            $line = preg_replace_callback(
                '|(\d{2})/(\d{2})/(\d{2})|',
                function ($matches) use(&$count){
                    $time = mktime(null, null, null, $matches[2], $matches[1], $matches[3]);
                    if (!$time) {
                        return $matches[0];
                    }
                    $count++;

                    return date('d-m-Y', $time);
                },
                $line
            );

            fwrite($fpOutput, $line);
        }
        fclose($fp);
        fclose($fpOutput);

        return $count;
    }

    public function output()
    {
        return $this->output;
    }
} 
