<?php

namespace App\Controller\API;

use App\Entity\RoverPhoto;
use App\Repository\RoverPhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PhotosController extends AbstractController
{
    #[Route('/api', name: 'api_photos')]
    public function photos(Request $request, RoverPhotoRepository $photoRepository): JsonResponse
    {
        $startDate   = $request->query->get('start_date');
        $endDate     = $request->query->get('end_date');
        $roverName   = $request->query->get('rover');
        $camera      = $request->query->get('camera');

        $photos = $photoRepository->getPhotosWithinRange(
            start: $startDate,
            end: $endDate,
            rover: $roverName,
            camera: $camera
        );
        return $this->json($this->manyPhotosToArray($photos));
    }

    #[Route(path: "/api/photo/{id<\d+>}", name: "api_photos_single-photo")]
    public function singlePhoto(RoverPhoto $photo): JsonResponse
    {
        $photoArray = $this->photoToArray($photo);
        unset($photoArray['id']);
        return $this->json($photoArray);
    }

    private function photoToArray(RoverPhoto $photo): array
    {
        $photoArray = [];
        $photoArray['id']          = $photo->getId();
        $photoArray['rover_name']  = $photo->getRoverName();
        $photoArray['camera_name'] = $photo->getCameraName();
        $photoArray['earth_date']  = $photo->getEarthDate()->format('Y-m-d');
        $photoArray['image_url']   = $photo->getImageURL();
        return $photoArray;
    }

    /** @param RoverPhoto[] $photos */
    private function manyPhotosToArray(mixed $photos): array
    {
        $allPhotos = [];
        foreach ($photos as $photo) {
            $allPhotos[] = $this->photoToArray($photo);
        }
        return $allPhotos;
    }
}
