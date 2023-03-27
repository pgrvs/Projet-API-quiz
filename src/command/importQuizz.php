<?php

namespace App\command;

use Symfony\Component\Console\Command\Command;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'app:import-quizz')]
class importQuizz extends Command
{

}