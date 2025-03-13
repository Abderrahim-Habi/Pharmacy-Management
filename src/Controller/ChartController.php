<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\ClientRepository;
use App\Repository\CommandeRepository;
use App\Repository\MedicamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/admin/rapport', name: 'admin.rapport.')]
class ChartController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ChartBuilderInterface $chartBuilder, MedicamentRepository $med,CommandeRepository $cmd,ClientRepository $clients): Response
    {
        // $medicaments = $med->findAll();
        $client = $clients->findAll();
        $commandes = $cmd->findAll();
        $commandesParDate = [];
        foreach ($commandes as $commande) {
            $dates = $commande->getdate()->format("Y-m-d");  
            if(!isset($commandesParDate[$dates])){
                $commandesParDate[$dates] = 0;
            }
            $commandesParDate[$dates] ++;
        }
        $dates = array_keys($commandesParDate);
        $nombresCommandes = array_values($commandesParDate);

        $medicaments = [];
        foreach ($dates as $date) {
            $medicaments[$date] = [];
            foreach ($commandes as $commande) {
                $date_cmd = $commande->getdate()->format("Y-m-d");
                if($date_cmd == $date){
                    foreach ($commande->getMedicaments() as $medicament) {
                        $medicaments[$date][] = $medicament->getPrice();    /*     $medicaments = [
                                                                                                    "2024-02-20" => ["Doliprane", "Aspirine", "Ibuprofène"],
                                                                                                    "2024-02-21" => ["Paracétamol"]
                                                                                                  ];*/
                    }
                }
            }
        }
        $price_dates = [];
        foreach($medicaments as $med){
            $price = array_values($med);
            $sum = 0;
            foreach($price as $prix){
                $sum += $prix;
            }
            $price_dates[]=$sum;
        }

        $totalVentes = array_sum($nombresCommandes);
        $totalRevenus = array_sum($price_dates);

        // Créer le graphique avec les données
        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => $dates,
            'datasets' => [
                [
                    'label' => 'Quantité de Ventes',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.7)', // Bleu
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 2,
                    'borderRadius' => 5,
                    'hoverBackgroundColor' => 'rgba(54, 162, 235, 1)',
                    'hoverBorderColor' => 'rgba(54, 162, 235, 1)',
                    'data' => $nombresCommandes,
                    'yAxisID' => 'y1', // Utilisation du premier axe Y
                ],
                [
                    'label' => 'Prix des Médicaments (MAD)',
                    'backgroundColor' => 'rgba(255, 159, 64, 0.7)', // Orange
                    'borderColor' => 'rgba(255, 159, 64, 1)',
                    'borderWidth' => 2,
                    'borderRadius' => 5,
                    'hoverBackgroundColor' => 'rgba(255, 159, 64, 1)',
                    'hoverBorderColor' => 'rgba(255, 159, 64, 1)',
                    'data' => $price_dates,
                    'yAxisID' => 'y2', // Utilisation du deuxième axe Y
                ],
            ],
        ]);

        $chart->setOptions([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'labels' => [
                        'font' => [
                            'size' => 14,
                            'weight' => 'bold',
                        ],
                        'color' => '#333',
                    ],
                ],
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'titleFont' => [
                        'size' => 14,
                        'weight' => 'bold',
                    ],
                    'bodyFont' => [
                        'size' => 12,
                    ],
                    'cornerRadius' => 6,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 12,
                        ],
                        'color' => '#333',
                    ],
                ],
                'y1' => [  // Axe Y pour les Quantités
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(200, 200, 200, 0.2)',
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 12,
                        ],
                        'color' => '#333',
                    ],
                ],
                'y2' => [  // Axe Y pour les Prix
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(200, 200, 200, 0.2)',
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 12,
                        ],
                        'color' => '#333',
                    ],
                    'position' => 'right',  // Positionner cet axe à droite
                ],
            ],
        ]);



        // Passer le graphique au template Twig
        return $this->render('chart/index.html.twig', [
            'clients' => $client,
            'chart' => $chart,
            'totalVentes' => $totalVentes,
            'totalRevenus' => $totalRevenus
        ]);
    }
    #[Route('/{id}',name:'show')]
    public function show(ClientRepository $clients,$id,ChartBuilderInterface $chartBuilder): Response{
        $client = $clients->find($id);
        $commandes = $client->getCommandes();  // Récupérer les commandes du client par son id
        $commandesParDate = [];
        foreach ($commandes as $commande) {
            $dates = $commande->getdate()->format("Y-m-d");  
            if(!isset($commandesParDate[$dates])){
                $commandesParDate[$dates] = 0;
            }
            $commandesParDate[$dates] ++;
        }
        $dates = array_keys($commandesParDate);
        $nombresCommandes = array_values($commandesParDate);

        $medicaments = [];
        foreach ($dates as $date) {
            $medicaments[$date] = [];
            foreach ($commandes as $commande) {
                $date_cmd = $commande->getdate()->format("Y-m-d");
                if($date_cmd == $date){
                    foreach ($commande->getMedicaments() as $medicament) {
                        $medicaments[$date][] = $medicament->getPrice();    /*     $medicaments = [
                                                                                                    "2024-02-20" => ["Doliprane", "Aspirine", "Ibuprofène"],
                                                                                                    "2024-02-21" => ["Paracétamol"]
                                                                                                  ];*/
                    }
                }
            }
        }
        $price_dates = [];
        foreach($medicaments as $med){
            $price = array_values($med);
            $sum = 0;
            foreach($price as $prix){
                $sum += $prix;
            }
            $price_dates[]=$sum;
        }

        $totalVentes = array_sum($nombresCommandes);
        $totalRevenus = array_sum($price_dates);

        // Créer le graphique avec les données
        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => $dates,
            'datasets' => [
                [
                    'label' => 'Quantité de Ventes',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.7)', // Bleu
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 2,
                    'borderRadius' => 5,
                    'hoverBackgroundColor' => 'rgba(54, 162, 235, 1)',
                    'hoverBorderColor' => 'rgba(54, 162, 235, 1)',
                    'data' => $nombresCommandes,
                    'yAxisID' => 'y1', // Utilisation du premier axe Y
                ],
                [
                    'label' => 'Prix des Médicaments (MAD)',
                    'backgroundColor' => 'rgba(255, 159, 64, 0.7)', // Orange
                    'borderColor' => 'rgba(255, 159, 64, 1)',
                    'borderWidth' => 2,
                    'borderRadius' => 5,
                    'hoverBackgroundColor' => 'rgba(255, 159, 64, 1)',
                    'hoverBorderColor' => 'rgba(255, 159, 64, 1)',
                    'data' => $price_dates,
                    'yAxisID' => 'y2', // Utilisation du deuxième axe Y
                ],
            ],
        ]);

        $chart->setOptions([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'labels' => [
                        'font' => [
                            'size' => 14,
                            'weight' => 'bold',
                        ],
                        'color' => '#333',
                    ],
                ],
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'titleFont' => [
                        'size' => 14,
                        'weight' => 'bold',
                    ],
                    'bodyFont' => [
                        'size' => 12,
                    ],
                    'cornerRadius' => 6,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 12,
                        ],
                        'color' => '#333',
                    ],
                ],
                'y1' => [  // Axe Y pour les Quantités
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(200, 200, 200, 0.2)',
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 12,
                        ],
                        'color' => '#333',
                    ],
                ],
                'y2' => [  // Axe Y pour les Prix
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(200, 200, 200, 0.2)',
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 12,
                        ],
                        'color' => '#333',
                    ],
                    'position' => 'right',  // Positionner cet axe à droite
                ],
            ],
        ]);
        return $this->render('chart/show.html.twig', [
            'name'=>$client->getName(),
            'client' => $client,
            'chart' => $chart,
            'totalVentes' => $totalVentes,
            'totalRevenus' => $totalRevenus
        ]);
    }

}