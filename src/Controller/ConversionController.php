<?php

namespace App\Controller;

use App\Entity\Conversion;
use App\Entity\User;
use App\Service\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

class ConversionController extends AbstractController
{
    #[Route('/api/conversion/mp3', name: 'app_conversion_mp3', methods: ['POST'])]
    public function newMp3Conversion(
        Request $request,
        Factory $factory,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $data = $request->getContent();

        try{
            /**
             * @var Conversion $conversion
             */
            $conversion = $serializer->deserialize($data, Conversion::class, "json");
        }catch(NotEncodableValueException $ex)
        {
            return $this->json(["message" => $ex->getMessage()], 400);
        }

        $conversion->setStatus(Conversion::STATUSES[0]);

        /**
         * @var User $user
         */
        $user = $this->getUser();
        $conversion->setUser($user);

        $path = $factory->convertMP3($conversion);

        $fileName = basename($path);

        $response = new BinaryFileResponse($path);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        return $this->json($response, 201);
    }

    #[Route('/api/conversion/mp4', name: 'app_conversion_mp4', methods: ['POST'])]
    public function newMp4Conversion(
        Request $request,
        Factory $factory,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $data = $request->getContent();

        try {
            /**
             * @var Conversion $conversion
             */
            $conversion = $serializer->deserialize($data, Conversion::class, "json");
        }
        catch (NotEncodableValueException $ex)
        {
            return $this->json(["message" => $ex->getMessage()], 400);
        }

        $conversion->setStatus(Conversion::STATUSES[0]);

        /**
         * @var User $user
         */
        $user = $this->getUser();
        $conversion->setUser($user);

        $path = $factory->convertMP4($conversion);

        $fileName = basename($path);

        $response = new BinaryFileResponse($path);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

        return $this->json($response, 201);
    }
}
