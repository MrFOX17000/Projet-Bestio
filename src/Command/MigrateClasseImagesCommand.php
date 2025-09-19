<?php
namespace App\Command;

use App\Entity\ClasseImage;
use App\Repository\ClasseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:migrate:classe-images',
    description: 'Copie Classe.image vers ClasseImage (position 0) si aucune image multiple.'
)]
class MigrateClasseImagesCommand extends Command
{
    // (supprimé: protected static $defaultName)

    public function __construct(
        private ClasseRepository $repo,
        private EntityManagerInterface $em
    ){
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('force', null, InputOption::VALUE_NONE, 'Appliquer réellement (sinon dry-run)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io    = new SymfonyStyle($input,$output);
        $force = $input->getOption('force');
        $count = 0;

        foreach ($this->repo->findAll() as $classe) {
            if (method_exists($classe,'getImage')
                && $classe->getImage()
                && $classe->getClasseImages()->count() === 0) {
                $ci = new ClasseImage();
                $ci->setPath($classe->getImage())->setPosition(0);
                $classe->addClasseImage($ci);
                $count++;
            }
        }

        if ($count === 0) {
            $io->success('Aucune migration nécessaire.');
            return Command::SUCCESS;
        }

        if ($force) {
            $this->em->flush();
            $io->success("$count image(s) migrée(s).");
        } else {
            $io->warning("$count enregistrement(s) seraient créés. Relance avec --force pour appliquer.");
        }

        return Command::SUCCESS;
    }
}