<?php

declare(strict_types=1);

namespace App\Controller;

use App\Components\User\UserFactory;
use App\Components\User\UserManager;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCreateAction extends AbstractController
{
    public function __construct(
        private readonly UserFactory $userFactory,
        private readonly UserManager $userManager,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function __invoke(#[MapRequestPayload] User $data): Response
    {
        $hashedPassword = $this->passwordHasher->hashPassword($data, $data->getPassword());
        $user = $this->userFactory->create($data->getEmail(), $hashedPassword);
        $this->userManager->save($user, true);

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
        ], Response::HTTP_CREATED);
    }
}
