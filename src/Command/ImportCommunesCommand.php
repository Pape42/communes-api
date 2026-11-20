<?php

namespace App\Command;

use App\Entity\CodePostal;
use App\Entity\Commune;
use App\Entity\Departement;
use App\Entity\Region;
use App\Repository\CodePostalRepository;
use App\Repository\CommuneRepository;
use App\Repository\DepartementRepository;
use App\Repository\RegionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


#[AsCommand(name: 'app:import-communes',
    description: 'Importe les communes d\'un département depuis l\'API Géo')]
class ImportCommunesCommand
{

    private array $regionsVues = [];
    private array $departementsVus = [];
    private array $codesPostauxVus = [];

    public function __construct(
        private HttpClientInterface $client,
        private EntityManagerInterface $em,
        private CommuneRepository $communes,
        private DepartementRepository $departements,
        private RegionRepository $regions,
        private CodePostalRepository $codesPostaux,
    ) {}

    public function __invoke(#[Argument('Le code du département')] string $departement,
        OutputInterface $output): int
    {

        $reponse = $this->client->request('GET',
            'https://geo.api.gouv.fr/departements/' . $departement . '/communes', ['query' => [
                'fields' => 'nom,code,codesPostaux,population,surface,departement,region']]);
        $donnees = $reponse->toArray();

        $crees = 0;
        foreach ($donnees as $donnee) {

            if (empty($donnee['code']) || empty($donnee['nom'])
                || empty($donnee['departement']) || empty($donnee['region'])) {
                continue;
            }
            // T - règle 2 : code INSEE déjà en base = déjà importée
            if ($this->communes->findOneBy(['codeInsee' => $donnee['code']])) {
                continue;
            }
  
            $region = $this->trouverOuCreerRegion($donnee['region']['code'], $donnee['region']['nom']);
            $dep = $this->trouverOuCreerDepartement(
                $donnee['departement']['code'], $donnee['departement']['nom'], $region);

      
            $commune = (new Commune())
                ->setCodeInsee($donnee['code'])
                ->setNom(mb_substr($donnee['nom'], 0, 255)) 
                ->setPopulation($donnee['population'] ?? null)
                ->setSurface($donnee['surface'] ?? null)
                ->setDepartement($dep);

            // T - règle 5 : chaque code postal retrouvé ou créé
            foreach ($donnee['codesPostaux'] ?? [] as $code) {
                $commune->addCodesPostaux($this->trouverOuCreerCodePostal($code));
            }


            $this->em->persist($commune);
            $crees++;
        }
        // L - ...et on écrit tout d'un coup, à la fin
        $this->em->flush();
        $output->writeln("$crees communes importées.");
        return Command::SUCCESS;
    }


    private function trouverOuCreerRegion(string $code, string $nom): Region
    {
        if (isset($this->regionsVues[$code])) {
            return $this->regionsVues[$code];        // déjà vue dans CET import
        }
        $region = $this->regions->findOneBy(['code' => $code]);
        if ($region === null) {
            $region = (new Region())->setCode($code)->setNom($nom);
            $this->em->persist($region);
        }
        return $this->regionsVues[$code] = $region;
    }


    private function trouverOuCreerDepartement(string $code, string $nom, Region $region): Departement
    {
        if (isset($this->departementsVus[$code])) {
            return $this->departementsVus[$code];
        }
        $departement = $this->departements->findOneBy(['code' => $code]);
        if ($departement === null) {
            $departement = (new Departement())->setCode($code)->setNom($nom)->setRegion($region);
            $this->em->persist($departement);
        }
        return $this->departementsVus[$code] = $departement;
    }

    private function trouverOuCreerCodePostal(string $code): CodePostal
    {
        if (isset($this->codesPostauxVus[$code])) {
            return $this->codesPostauxVus[$code];
        }
        $codePostal = $this->codesPostaux->findOneBy(['code' => $code]);
        if ($codePostal === null) {
            $codePostal = (new CodePostal())->setCode($code);
            $this->em->persist($codePostal);
        }
        return $this->codesPostauxVus[$code] = $codePostal;
    }
}