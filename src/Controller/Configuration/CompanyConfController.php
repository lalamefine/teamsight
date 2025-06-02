<?php

namespace App\Controller\Configuration;

use App\Abstraction\AbstractCompanyController;
use App\Entity\CompanyConfig;
use App\Entity\Feedback360\ObsProfile;
use App\Repository\Feedback360\ObsProfileRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CompanyConfController extends AbstractCompanyController
{
    #[Route('/cf/company', name: 'app_conf_company')]
    public function index(Request $request): Response
    {        
        // Initialisation de la configuration par défaut si elle n'existe pas
        $companyConfig = $this->getCompany()->getConfig()??(function () {
            $cc = new CompanyConfig();
            $cc->setCompany($this->getCompany());
            $this->em->persist($cc);
            $this->em->flush();
            return $cc;
        })();
        
        // Traitement du formulaire si POST
        if ($request->isMethod('POST')) {
            // Récupération des données du formulaire
            $formData = $request->request->all();
            
            // Traiter les profils d'observateurs si nécessaire
            if (isset($formData['obsp_lib']) && isset($formData['obsp_id']) && isset($formData['obsp_anon'])) {
                $this->handleObserverProfiles($formData);
            }
            
            // Mise à jour de la configuration
            $companyConfig->setQuestFdb360(isset($formData['QuestFdb360']));
            $companyConfig->setQuestComp(isset($formData['QuestComp']));
            $companyConfig->setQuestEA(isset($formData['QuestEa']));
            $companyConfig->setQuestPerc(isset($formData['QuestPerc']));
            
            $companyConfig->setAgtIdType($formData['AgtIdType'] ?? 'email');
            $companyConfig->setAgtAuthType($formData['AgtAuthType'] ?? 'email-pass');
            
            // $companyConfig->setUseTeamGrouping(isset($formData['UseAgtEquipe']));
            $companyConfig->setUseCompRef(isset($formData['UseCompRef']));
            $companyConfig->setUseAccountDynCamp(isset($formData['UseAccountDynCamp']));
            $companyConfig->setUseAccountDynPan(isset($formData['UseAccountDynPan']));
            
            $companyConfig->setDataRetention((int)($formData['DataRetention'] ?? 36));
            
            $this->em->flush();
            $this->addFlash('success', 'Configuration enregistrée avec succès.');
        }
        
        return $this->render('configuration/confCompany/index.html.twig', [
            'company' => $this->getCompany(),
            'companyConfig' => $companyConfig
        ]);
    }
    
    /**
     * Gère les profils d'observateurs
     */
    private function handleObserverProfiles(array $formData): void
    {
        /** @var ObsProfileRepository $obsProfileRepository */
        $obsProfileRepository = $this->em->getRepository(ObsProfile::class);
        // Supprimer les profils marqués pour suppression
        if (isset($formData['obsp_del']) && is_array($formData['obsp_del'])) {
            foreach ($formData['obsp_del'] as $id) {
                $profile = $obsProfileRepository->findOneBy(['id' => $id, 'company' => $this->getCompany()]);
                if ($profile && $profile->isEditable()) {
                    $this->em->remove($profile);
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
                $profile = new ObsProfile($name, $isAnonymous, $this->getCompany());
                $this->em->persist($profile);
            } else {
                // Profil existant
                $profile = $obsProfileRepository->findOneBy(['id' => $id, 'company' => $this->getCompany()]);
                if (!$profile || !$profile->isEditable()) {
                    continue;
                }
            }
            
            $profile->setName($name);
            $profile->setAnonymous($isAnonymous);
        }
    }

    #[Route('/cf/company-campaigns', name: 'app_conf_company_campaigns')]
    public function companyConfCamp(Request $request): Response
    {
        // Initialisation de la configuration par défaut si elle n'existe pas
        $companyConfig = $this->getCompany()->getConfig()??(function () {
            $cc = new CompanyConfig();
            $cc->setCompany($this->getCompany());
            $this->em->persist($cc);
            $this->em->flush();
            return $cc;
        })();

        // Traitement du formulaire si POST
        if ($request->isMethod('POST')) {
            // Récupération des données du formulaire
            $formData = $request->request->all();
           
            $companyConfig->setUseAccountDynCamp(isset($formData['UseAccountDynCamp']));
            $companyConfig->setUseAccountDynPan(isset($formData['UseAccountDynPan']));
            $this->em->flush();
            $this->addFlash('success', 'Configuration enregistrée avec succès.');
        }

        return $this->render('configuration/confCompany/campaign.html.twig', [ 
            'company' => $this->getCompany(),
            'companyConfig' => $companyConfig,
        ]);
    }


}
