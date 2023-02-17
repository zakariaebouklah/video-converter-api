<?php

namespace App\Controller;

use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Entity\Conversion;
use App\Entity\User;
use App\Service\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

class ConversionController extends AbstractController
{
    #[Route('/api/conversion/mp3', name: 'app_conversion_mp3', methods: ['POST'])]
    public function newMp3Conversion(
        Request $request,
        Factory $factory,
        SerializerInterface $serializer
    ): Response
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

        try {
            $factory->convertMP3($conversion);
        }catch (ProcessFailedException $exception)
        {
            return $this->json(["success" => false]);
        }

        return $this->json(
            [
                "success" => true,
                "link" => $this->generateUrl
                (
                    "app_download",
                    ["id" => $conversion->getId()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            ]
        );
    }

    #[Route('/download/{id}', name: "app_download", methods: ['GET'])]
    public function downloadRoute(
        Conversion $conversion
    ): Response
    {
        if ($conversion->getStatus() !== Conversion::STATUSES[2])
        {
            throw new BadRequestHttpException();
        }

        /**
         * @var string $outputFile
         */
        $outputFile = $conversion->getFilePath();
        /**
         * @var BinaryFileResponse $response
         */
        $response =  new BinaryFileResponse($outputFile);

        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, basename($outputFile));
//        $response->deleteFileAfterSend();

        return $response;
    }


    #[Route('/api/conversion/mp4', name: 'app_conversion_mp4', methods: ['POST'])]
    public function newMp4Conversion(
        Request $request,
        Factory $factory,
        SerializerInterface $serializer
    ): Response
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

        try {
            $factory->convertMP4($conversion);
        }
        catch (ProcessFailedException $ex)
        {
            return $this->json(["success" => false]);
        }

        return $this->json([
            "success" => true,
            "link" => $this->generateUrl
            (
                "app_download",
                ["id" => $conversion->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
        ]);
    }
}
