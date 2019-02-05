<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AddDefaultUserCommand extends Command
{
    const DEFAULT_EMAIL = 'default@user.co.uk';
    const DEFAULT_PASSWORD = 'test123';
    protected static $defaultName = 'addDefaultUserCommand';

    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Creates default user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if ($this->hasUser()) {
            $io->success('User already created.');

            return;
        }

        $user = new User();
        $user->setEmail(self::DEFAULT_EMAIL);
        $user->setRoles([]);
        $password = $this->passwordEncoder->encodePassword($user, self::DEFAULT_PASSWORD);
        $user->setPassword($password);
        $this->userRepository->save($user);

        $io->success('Create user successful.');
    }

    /**
     * Check default user exists.
     *
     * @return bool
     */
    protected function hasUser()
    {
        if ($this->userRepository->findByEmail(self::DEFAULT_EMAIL)) {
            return true;
        }

        return false;
    }
}
