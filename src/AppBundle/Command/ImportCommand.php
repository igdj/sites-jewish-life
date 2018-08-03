<?php

// src/AppBundle/Command/GreetCommand.php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class ImportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sites:import')
            ->setDescription('Import Sites')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $fname = $this->getContainer()->get('kernel')->getRootDir()
               . '/Resources/data/sites.xlsx';

        $fs = new Filesystem();

        if (!$fs->exists($fname)) {
            $output->writeln(sprintf('<error>%s does not exist</error>', $fname));
            return 1;
        }

        $file = new \SplFileObject($fname);
        $reader = new \Ddeboer\DataImport\Reader\ExcelReader($file);

        $reader->setHeaderRowNumber(0);
        $count = 0;

        $entityManager = $this->getContainer()->get('doctrine')->getEntityManager();
        $siteRepository = $entityManager->getRepository('AppBundle:Site');

        foreach ($reader as $row) {
            $unique_values = array_unique(array_values($row));
            if (1 == count($unique_values) && null === $unique_values[0]) {
                // all values null
                continue;
            }

            if (empty($row['ID'])) {
                continue;
            }

            $output->writeln('Insert/Update: ' . $row['ID']);

            $site = $siteRepository->findOneById($row['ID']);
            if (is_null($site)) {
                $site = new \AppBundle\Entity\Site();
                $site->setId($row['ID']);
            }

            foreach ($row as $key => $value) {
                switch ($key) {
                    case 'marker':
                    case 'street':
                    case 'latitude':
                    case 'longitude':
                    case 'url':
                        $site->{$key} = $value;
                        break;

                    case 'title_de':
                    case 'description_de':
                    case 'title_en':
                    case 'description_en':
                    case 'streetoverride_en':
                        list($field, $lang) = explode('_', $key, 2);
                        if ('streetoverride' == $field) {
                            $field = 'streetOverride';
                        }
                        $currentValues = $site->{$field};
                        if (is_null($currentValues)) {
                            $currentValues = [];
                        }
                        $currentValues[$lang] = $value;
                        $site->{$field} = $currentValues;
                        break;

                    case 'dasjuedischehamburg':
                        $currentValues = $site->additional;
                        if (!empty($value)) {
                            if (is_null($currentValues)) {
                                $currentValues = [];
                            }
                            $currentValues[$key] = $value;
                        }
                        else if (!is_null($currentValues)) {
                            unset($currentValues[$key]);
                        }
                        $site->additional = $currentValues;
                        break;

                    default:
                        // $output->writeln('Skip : ' . $key);
                }
            }
            $entityManager->persist($site);
        }

        $entityManager->flush();
    }
}
