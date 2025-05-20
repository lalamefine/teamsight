<?php

namespace App\Controller\Configuration;

use App\Entity\Company;
use App\Entity\CompanyConfig;
use App\Entity\ObsProfile;
use App\Entity\WebUser;
use App\Repository\ObsProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class CompanyConfController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ObsProfileRepository $obsProfileRepository,
    ){}

    #[Route('/cf/company', name: 'app_conf_company')]
    public function index(Request $request, EntityManagerInterface $em, #[CurrentUser] ?WebUser $user = null): Response
    {
        // Récupérer la company active pour l'utilisateur courant
        $company = $user ? $user->getCompany() : null;
        
        if (!$company) {
            throw $this->createNotFoundException('Aucune entreprise associée à l\'utillisateur actif');
        }
        
        // Initialisation de la configuration par défaut si elle n'existe pas
        if (!$company->getConfig()) {
            $cc = new CompanyConfig();
            $company->setConfig($cc);
            $em->persist($cc);            
            $em->flush();
        }
        
        $companyConfig = $company->getConfig();
        $confSaved = false;
        
        // Traitement du formulaire si POST
        if ($request->isMethod('POST')) {
            // Récupération des données du formulaire
            $formData = $request->request->all();
            
            // Traiter les profils d'observateurs si nécessaire
            if (isset($formData['obsp_lib']) && isset($formData['obsp_id']) && isset($formData['obsp_anon'])) {
                $this->handleObserverProfiles($formData, $company, $em);
            }
            
            // Mise à jour de la configuration
            $companyConfig->setQuestFdb360(isset($formData['QuestFdb360']));
            $companyConfig->setQuestComp(isset($formData['QuestComp']));
            $companyConfig->setQuestEA(isset($formData['QuestEa']));
            $companyConfig->setQuestPerc(isset($formData['QuestPerc']));
            
            $companyConfig->setAgtIdType($formData['AgtIdType'] ?? 'email');
            $companyConfig->setAgtAuthType($formData['AgtAuthType'] ?? 'email-pass');
            $companyConfig->setAccountSystem($formData['AccountSystem'] ?? 'WebUI');
            
            $companyConfig->setUseTeamGrouping(isset($formData['UseAgtEquipe']));
            $companyConfig->setUseCompRef(isset($formData['UseCompRef']));
            $companyConfig->setUseAccountDynCamp(isset($formData['UseAccountDynCamp']));
            $companyConfig->setUseAccountDynPan(isset($formData['UseAccountDynPan']));
            
            $companyConfig->setDataRetention((int)($formData['DataRetention'] ?? 36));
            
            $em->flush();
            $confSaved = true;
            $this->addFlash('success', 'Configuration enregistrée avec succès.');
        }
        
        return $this->render('configuration/confCompany/index.html.twig', [
            'company' => $company,
            'companyConfig' => $companyConfig,
            'confSaved' => $confSaved
        ]);
    }
    
    /**
     * Gère les profils d'observateurs
     */
    private function handleObserverProfiles(array $formData, Company $company, EntityManagerInterface $em): void
    {
        // Supprimer les profils marqués pour suppression
        if (isset($formData['obsp_del']) && is_array($formData['obsp_del'])) {
            foreach ($formData['obsp_del'] as $id) {
                $profile = $this->obsProfileRepository->findOneBy(['id' => $id, 'company' => $company]);
                if ($profile && $profile->isEditable()) {
                    $em->remove($profile);
                }
            }
        }
        
        // Mettre à jour les profils existants et ajouter les nouveaux
        $count = count($formData['obsp_lib'] ?? []);
        for ($i = 0; $i < $count; $i++) {
            $id = $formData['obsp_id'][$i] ?? null;
            $name = $formData['obsp_lib'][$i] ?? '';
            $isAnonymous = ($formData['obsp_anon'][$i] ?? 'on') === 'on';
            
            if ($id === 'new') {
                // Nouveau profil
                $profile = new ObsProfile($name, $isAnonymous, $company);
                $em->persist($profile);
            } else {
                // Profil existant
                $profile = $this->obsProfileRepository->findOneBy(['id' => $id, 'company' => $company]);
                if (!$profile || !$profile->isEditable()) {
                    continue;
                }
            }
            
            $profile->setName($name);
            $profile->setAnonymous($isAnonymous);
        }
    }
}
