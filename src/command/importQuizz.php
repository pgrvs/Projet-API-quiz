<?php

namespace App\command;

use App\Entity\Categorie;
use App\Entity\Question;
use App\Entity\Reponse;
use App\Repository\CategorieRepository;
use App\Repository\QuestionRepository;
use App\Repository\ReponseRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use League\Csv\Reader;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Console\Helper\ProgressBar;


// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'app:import-quizz')]
class importQuizz extends Command
{
    private CategorieRepository $categorieRepository;
    private QuestionRepository $questionRepository;
    private ReponseRepository $reponseRepository;
    private SluggerInterface $slugger;

    /**
     * @param CategorieRepository $categorieRepository
     * @param QuestionRepository $questionRepository
     * @param ReponseRepository $reponseRepository
     * @param SluggerInterface $slugger
     */
    public function __construct(CategorieRepository $categorieRepository, QuestionRepository $questionRepository, ReponseRepository $reponseRepository, SluggerInterface $slugger)
    {
        $this->categorieRepository = $categorieRepository;
        $this->questionRepository = $questionRepository;
        $this->reponseRepository = $reponseRepository;
        $this->slugger = $slugger;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->reponseRepository->findAll() as $reponse){
            $this->reponseRepository->remove($reponse);
        }
        foreach ($this->questionRepository->findAll() as $question){
            $this->questionRepository->remove($question);
        }

        $reader = Reader::createFromPath('src/Command/quizz.csv', 'r');
        $reader->setDelimiter(';');
        $reader->setHeaderOffset(0);
        $records = $reader->getRecords();

        $progressBar = new ProgressBar($output, count($reader));

        foreach ($records as $offset => $record) {
            $progressBar->advance();
            $categorie = $this->categorieRepository->findOneBy(['libelle' => $record['categorie']]);

            if (! $categorie){
                $categorie = new Categorie();
                $categorie->setLibelle($record['categorie']);
                $categorie->setSlug($this->slugger->slug($record['categorie'])->lower());
                $this->categorieRepository->save($categorie, true);
            }

            $question = new Question();
            $question->setLibelleQuestion($record['question']);
            $question->setCategorie($categorie);

            $this->questionRepository->save($question, true);

            for ($i=1; $i<=4; $i++){
                $reponse = new Reponse();
                $reponse->setLibelleReponse($record['reponse'. $i]);
                $reponse->setQuestion($question);
                if ($i === 1){
                    $reponse->setIsTrue(true);
                } else {
                    $reponse->setIsTrue(false);
                }

                $this->reponseRepository->save($reponse, true);
            }

        }
        $progressBar->finish();
        return Command::SUCCESS;
    }
}