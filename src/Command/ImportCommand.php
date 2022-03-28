<?php

// src/App/Command/ImportCommand.php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

use Symfony\Contracts\HttpClient\HttpClientInterface;

use Doctrine\ORM\EntityManagerInterface;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class ImportCommand extends Command
{
    protected $em;
    protected $rootDir;
    protected $httpClients = [];

    public function __construct(EntityManagerInterface $em,
                                HttpClientInterface $deClient,
                                HttpClientInterface $enClient,
                                HttpClientInterface $dasjuedischehamburgClient,
                                $rootDir)
    {
        parent::__construct();

        $this->em = $em;
        $this->httpClients = [
            'de' => $deClient,
            'en' => $enClient,
            'dasjuedischehamburg' => $dasjuedischehamburgClient,
        ];
        $this->rootDir = $rootDir;
    }

    protected function configure()
    {
        $this
            ->setName('sites:import')
            ->setDescription('Import Sites')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fname = $this->rootDir
               . '/data/Stadtplan_Erweiterung.xlsx';

        $fs = new Filesystem();

        if (!$fs->exists($fname)) {
            $output->writeln(sprintf('<error>%s does not exist</error>',
                                     $fname));

            return 1;
        }

        $reader = ReaderEntityFactory::createReaderFromFile($fname);

        $reader->open($fname);

        $count = 0;

        $siteRepository = $this->em->getRepository('\App\Entity\Site');

        $headers = [];

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $rowObj) {
                $values = $rowObj->toArray();

                $unique_values = array_unique(array_values($values));
                if (1 == count($unique_values) && null === $unique_values[0]) {
                    // all values null
                    continue;
                }

                if (empty($headers)) {
                    $headers = $values;
                    continue;
                }

                // make assoc
                $row = [];
                foreach ($headers as $i => $key) {
                    $row[$key] = array_key_exists($i, $values) ? trim($values[$i]) : null;
                }


                if (empty($row['ID'])) {
                    continue;
                }

                $output->writeln('Insert/Update: ' . $row['ID']);

                $site = $siteRepository->findOneById($row['ID']);
                if (is_null($site)) {
                    $site = new \App\Entity\Site();
                    $site->setId($row['ID']);
                }

                foreach ($row as $key => $value) {
                    switch ($key) {
                        case 'marker':
                        case 'street':
                        case 'latitude':
                        case 'longitude':
                        case 'author':
                        case 'url':
                        case 'topic':
                            $site->{$key} = trim($value);
                            break;

                        case 'physical':
                            if (isset($value) && (bool)$value) {
                                $site->setFlags(\App\Entity\Site::FLAG_PHYSICAL);
                            }
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

                            $currentValues[$lang] = trim($value);
                            $site->{$field} = $currentValues;
                            break;

                        case 'dasjuedischehamburg':
                        case 'related':
                        case 'keydocuments':
                            $currentValues = $site->additional;
                            if (!empty($value)) {
                                if (is_null($currentValues)) {
                                    $currentValues = [];
                                }

                                if ($key == 'related') {
                                    $currentValues[$key] =  preg_split('/\s*,\s*/', $value);
                                }
                                else if ($key == 'keydocuments') {
                                    $currentValues[$key] = $this->buildKeydocuments(preg_split('/\s*;\s*/', $value));
                                }
                                else {
                                    $value = preg_replace('#https?://[www\.]*dasjuedischehamburg\.de/inhalt/#', '', $value);

                                    $currentValues[$key] = $this->buildJuedischeHamburg(preg_split('/\s*;\s*/', $value));
                                }

                            }
                            else if (!is_null($currentValues)) {
                                unset($currentValues[$key]);
                            }

                            $site->additional = $currentValues;
                            break;

                        case 'rights_1_de':
                            $key = 'media';
                            $currentValues = $site->additional;

                            if (!empty($value)) {
                                if (is_null($currentValues)) {
                                    $currentValues = [];
                                }

                                $mediaPath = '/web/media/';
                                $media = [];

                                $captions = preg_split('/\s*;\s*/', $value);
                                $captions_en = preg_split('/\s*;\s*/', $row['rights_1_eng']);

                                for ($i = 0; $i < count($captions); $i++) {
                                    $append = '';
                                    if (1 == $i) {
                                        $append = 'b';
                                    }
                                    else if ($i > 1) {
                                        die('TODO: Handle multiple images in ' . $row['ID']);
                                    }

                                    $fname = sprintf('ID_%s%s.jpg',
                                                     $row['ID'], $append);
                                    if (!file_exists($this->rootDir . $mediaPath . $fname)) {
                                        die($fname . ' does not exist');
                                    }

                                    $media[] = [
                                        'de' => [
                                            'url' => '/media/' . $fname,
                                            'caption' => $captions[$i],
                                        ],
                                        'en' => [
                                            'url' => '/media/' . $fname,
                                            'caption' => $captions_en[$i],
                                        ],
                                    ];
                                }

                                $currentValues[$key] = $media;
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

                $this->em->persist($site);
            }

            break; // only get first sheet
        }

        $this->em->flush();

        return 0;
    }

    private function buildKeydocuments($uids)
    {
        static $articleInfo = [];

        $prefixes = [
            'de' => [
                'article' => 'beitrag',
                'source' => 'quelle',
            ],
        ];

        $ret = [];

        foreach ($uids as $uid) {
            if (array_key_exists($uid, $articleInfo)) {
                $ret[] = $articleInfo[$uid];
                continue;
            }

            $info = [];

            if (preg_match('/^jgo\:((article|source)\-\d+)$/', $uid, $matches)) {
                foreach ([ 'de', 'en' ] as $locale) {
                    $pathParts = [
                        array_key_exists($locale, $prefixes)
                        && array_key_exists($matches[2], $prefixes[$locale])
                        ?  $prefixes[$locale][$matches[2]]
                        :  $matches[2],
                        $matches[0],
                    ];

                    $path = '/' . join('/', $pathParts);
                    $apiResponse = $this->httpClients[$locale]->request('GET', $path . '.jsonld');

                    $schema = json_decode($apiResponse->getContent(), true);
                    if (false === $schema) {
                        die('Lookup for ' . $url . ' failed');
                    }

                    if (array_key_exists('author', $schema)) {
                        $creator = array_key_exists('name', $schema['author'])
                            ? $schema['author']['name']
                            : join(', ', array_map(function ($author) { return $author['name']; }, $schema['author']));
                    }
                    else {
                        $creator = $schema['creator'];
                    }

                    $info[$locale] = [
                        'name' => $schema['name'],
                        'creator' => $creator,
                        'url' => str_replace('.jsonld', '', $apiResponse->getInfo('url')),
                    ];
                }

                $ret[] = $articleInfo[$uid] = $info;
            }
            else {
                die('Invalid keydocuments uid: ' . $uid);
            }
        }

        return $ret;
    }

    private function buildJuedischeHamburg($slugs)
    {
        static $articleInfo = [];

        $ret = [];

        foreach ($slugs as $slug) {
            if (array_key_exists($slug, $articleInfo)) {
                $ret[] = $articleInfo[$slug];
                continue;
            }

            $path = $slug;
            $htmlResponse = $this->httpClients['dasjuedischehamburg']->request('GET', $path);

            $crawler = new Crawler($htmlResponse->getContent());
            $name = $crawler->filter('h1.title')->text();

            $info = [
                'name' =>$name,
                'url' => $htmlResponse->getInfo('url'),
            ];

            $ret[] = $articleInfo[$slug] = $info;
        }

        return $ret;
    }
}
