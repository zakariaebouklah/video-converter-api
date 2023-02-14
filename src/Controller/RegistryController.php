<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

class RegistryController extends AbstractController
{
    #[Route('/register', name: 'app_registry', methods: ['POST'])]
    public function addUser(
        Request $request,
        SerializerInterface $serializer,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $manager,
        UserRepository $repository
    ): JsonResponse
    {
        $data = $request->getContent();

        try {
            /**
             * @var User $user
             */
            $user = $serializer->deserialize($data, User::class, "json");

            //What if the user that want to register already exists in database ?

            $alreadyExistingUser = $repository->findOneBy(['email' => $user->getEmail()]);

            if($alreadyExistingUser != null)
            {
                return $this->json([
                    "message" => "Account Already Existing..."
                ], 406);
            }
        }
        catch (NotEncodableValueException $ex)
        {
            return $this->json([
                "message" => $ex->getMessage()
            ], 400);
        }

        $user->setPassword($hasher->hashPassword($user, $user->getPassword()));

        $manager->persist($user);
        $manager->flush();

        return $this->json([
            'message' => 'new user has been registered to the app...',
            'status' => 201
        ], 201);
    }
}
