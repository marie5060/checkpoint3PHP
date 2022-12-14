<?php

namespace App\Controller;

use App\Entity\Boat;
use App\Repository\BoatRepository;
use App\Service\MapManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/boat')]
class BoatController extends AbstractController
{
    #[Route('/move/{x}/{y}', name: 'moveBoat', requirements: ['x' => '\d+', 'y' => '\d+'])]
    public function moveBoat(int $x, int $y, BoatRepository $boatRepository, EntityManagerInterface $em): Response
    {
        $boat = $boatRepository->findOneBy([]);
        $boat->setCoordX($x);
        $boat->setCoordY($y);
        $em->flush();
        return $this->redirectToRoute('map');
    }

    #[Route('/direction/{direction}', name: 'moveDirection', requirements: ['direction' => '[NSEW]'])]
    public function moveDirection(string $direction, BoatRepository $boatRepository, EntityManagerInterface $em, MapManager $mapManager): Response
    {
       
        $boat = $boatRepository->findOneBy([]);

        if ($direction === "E")
        {
          $x = $boat->getCoordX()+1;
          $boat->setCoordX($x);

        } else if ($direction === "W")
        {
            $x = $boat->getCoordX()-1;
            $boat->setCoordX($x);

        } else if ($direction === "S")
        {
            $y = $boat->getCoordY()+1;
            $boat->setCoordY($y);

        } else if ($direction === "N")
        {
            $y = $boat->getCoordY()-1;
            $boat->setCoordY($y);
        }

        //test de la method tileExists et retour d'une erreur si tile n'existe pas sinon alors avancé du bateau 
        $tile = $mapManager->tileExists($boat->getCoordX(),$boat->getcoordY());
        if (!$tile){
            $this->addFlash('warning', 'loupé');
            return $this->redirectToRoute('map');
        }
      
        $em->flush();
        return $this->redirectToRoute('map');
    }
}