<?php

namespace App\Command;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

#[AsCommand(
    name: 'app:import-csv-file-data',
    description: 'Importer les produits du fichier CSV',
)]
class ImportCsvFileCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private string $dataDirectory;
    private SymfonyStyle $io;
    private ProductsRepository $productRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        string $dataDirectory,
        ProductsRepository $productRepository,

    )
    {
        parent::__construct();
        $this->dataDirectory = $dataDirectory;
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->createProduct();

        return Command::SUCCESS;
    }

    private function getDataFromFile(): array
    {
        $file = $this->dataDirectory . '/produits.csv';

        $normalizers =  [new ObjectNormalizer()];

        $encoders = [new CsvEncoder()];

        $serializer = new Serializer($normalizers, $encoders);

        $fileString = file_get_contents($file);
        $lines = explode(PHP_EOL, $fileString);
        $array = [];
        foreach ($lines as $line) {
            $array[] = str_getcsv($line);
        }

        return $array;
    }

    private function createProduct():void
    {
        $datas = $this->getDataFromFile();

        $this->io->section('CREATION DES PRODUITS');

        foreach ($datas as $data) {
            $product = new Products();

            if (isset($data[1])) {
                $product->setIsbn13($data[1]);
            } else {
                $product->setIsbn13("");
            }
            if (isset($data[2])) {
                $product->setTitle($data[2]);
            } else {
                $product->setTitle("");
            }
            if (isset($data[3])) {
                $product->setAuthor($data[3]);
            } else {
                $product->setAuthor("");
            }

            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();
    }

}
