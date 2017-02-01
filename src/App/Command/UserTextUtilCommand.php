<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserTextUtilCommand extends Command
{

    /**
     * Configures the command
     */
    protected function configure()
    {
        $this->setName('user_text_util')->setDescription('User Text Util')->addArgument(
            'separator',
            InputArgument::REQUIRED,
            'Specify the separator'
        )->addArgument(
                'action',
                InputArgument::REQUIRED,
                'Specify the action'
            );
    }

    /**
     * Executes the current command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $separator = $input->getArgument('separator');
        $actionName = $input->getArgument('action');

        $filePeopleSource = '/var/data/people/people.csv';
        $pathPeopleTextSource = '/var/data/people/texts/';
        $pathPeopleTextOutput = '/var/data/people/output_texts/';
        $csvDelimiter = self::getCsvDelimiterByCode($separator);

        if($csvDelimiter === null){
            throw new \ErrorException('Unknown separator. Possible variants: coma, semicolon');
        }

        switch ($actionName) {
            case 'countAverageLineCount':
                $action = new UserTextUtilCountAverage($csvDelimiter, $filePeopleSource, $pathPeopleTextSource);
                break;

            case 'replaceDates':
                $action = new UserTextUtilReplaceDates(
                    $csvDelimiter,
                    $filePeopleSource,
                    $pathPeopleTextSource,
                    $pathPeopleTextOutput
                );
                break;

            default:
                throw new \ErrorException('Unknown action. Possible variants: countAverageLineCount, replaceDates');
        }

        $action();
        $output->writeln($action->output());
    }

    /**
     * Возвращает csv разделитель по его коду
     *
     * @param $code
     *
     * @return null|string
     */
    private static function getCsvDelimiterByCode($code)
    {
        $code = strtolower($code);
        switch ($code) {
            case 'coma':
                return ',';

            case 'semicolon':
                return ';';

            default:
                return null;
        }
    }
} 