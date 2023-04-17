<?php

namespace App\Command;

use App\Factory\ProductFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:add-product',
    description: 'Add a product to DB',
)]
class AddProductCommand extends Command
{

    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Product name')
            ->addArgument('price', InputArgument::REQUIRED, 'Product price')
            ->addArgument('description', InputArgument::OPTIONAL, 'Product description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');
        $price = $input->getArgument('price');

        if ($name) {
            $io->note(sprintf('Name: %s', $name));
        }

        if ($price) {
            $io->note(sprintf('Price: %f', $price));
        }

        $product = ProductFactory::create($name, (float) $price);
        $this->em->persist($product);
        $this->em->flush();

        $io->success('Product persisted with id: ' . $product->getId());

        return Command::SUCCESS;
    }
}
