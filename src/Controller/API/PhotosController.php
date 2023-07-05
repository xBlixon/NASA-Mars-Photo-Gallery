<?php

namespace App\Controller\API;

use App\Entity\RoverPhoto;
use App\Repository\RoverPhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        return $this->json($this->photosToArray($photos));
    }
    /** @param RoverPhoto[] $photos */
    private function photosToArray(mixed $photos): array
    {
        $allPhotos = [];
        foreach ($photos as $photo) {
            $photoArray = [];
            $photoArray['id']          = $photo->getId();
            $photoArray['rover_name']  = $photo->getRoverName();
            $photoArray['camera_name'] = $photo->getCameraName();
            $photoArray['earth_date']  = $photo->getEarthDate()->format('Y-m-d');
            $photoArray['image_url']   = $photo->getImageURL();
            $allPhotos[] = $photoArray;
        }
        return $allPhotos;
    }

    #[Route(path: "/api/photo/{id<\d+>}", name: "api_photos_single-photo")]
    public function singlePhoto(): JsonResponse
    {
        return $this->json([]);
    }
}
