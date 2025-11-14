<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\MailProvider;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MailProviderRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'initialize',
    description: 'Initialize the application and create the default admin user',
)]
class InitializeCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        private MailProviderRepository $mailProviderRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->createUser($io);
        //$this->createProvider($io);

        return Command::SUCCESS;
    }

    private function createUser($io)
    {
        $email = 'setheve@viceversa.re';

        // Vérification existence utilisateur
        $existingUser = $this->userRepository->findOneBy(['email' => $email]);

        if ($existingUser) {
            $io->warning('L’utilisateur existe déjà : ' . $email);
            return Command::SUCCESS;
        }

        // Création utilisateur
        $user = new User();
        $user->setEmail($email);
        $user->setUserName('Samuel ETHEVE');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'Samuel974')
        );

        $this->em->persist($user);
        $this->em->flush();
        $io->success('Utilisateur administrateur créé : ' . $email);

        return Command::SUCCESS;
    }

    private function createProvider($io)
    {
        $providers = [
            'Gmail',
            'SMTP',
        ];

        foreach ($providers as $providerSingle) {
            $existingProvider = $this->mailProviderRepository->findOneBy(['title' => $providerSingle]);

            if ($existingProvider) {
                $io->warning('Provider existe déjà : ' . $providerSingle);
                continue;
            } else {
                $provider = new MailProvider();
                $provider->setCreatedAt(new \DateTimeImmutable());
                $provider->setTitle($providerSingle);
                $io->success('Provider créé : ' . $provider->getTitle());
            }
        }

        $this->em->persist($provider);
        $this->em->flush();

        return Command::SUCCESS;
    }
}
