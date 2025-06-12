<?php 

namespace App\Service;

use App\Entity\Feedback360\CampaignFeedback360;
use App\Entity\Feedback360\Observation360;
use App\Entity\Feedback360\Observer;
use App\Entity\Feedback360\ObsProfile;
use App\Entity\WebUser;
use App\Repository\Feedback360\ObsProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class Obs360Service
{
    private ?ObsProfile $observedProfile = null;
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private ObsProfileRepository $obsProfileRepository,
    )  { }

    private function getCurrentUser(): ?WebUser
    {
        return $this->security->getUser();
    }

    public function startObservation(CampaignFeedback360 $campaign, WebUser $webUser): void
    {
        if ($campaign->isStateOrAfter(CampaignFeedback360::STATE_ANS_CLOSED)) {
            throw new \Exception('La campagne n\'est pas active.');
        }

        // Check if the user is eligible to start an observation
        // if (!$campaign->isUserEligible($webUser)) {
        //     throw new \Exception('L\'utilisateur n\'est pas éligible pour démarrer une observation.');
        // }

        if(!$this->observedProfile){
            $this->observedProfile = $this->obsProfileRepository->findOneBy([
                'company' => $campaign->getCompany(),
                'canValidateReport' => false,
                'canSeeValidatedReport' => true,
                'editable' => false,
            ]);
        }

        $obs = new Observation360($campaign, $webUser);
        $observed = new Observer(
            $obs,
            $webUser,
            $this->observedProfile
        );
        $obs->getObservers()->add($observed);
        $this->em->persist($obs);
        $this->em->persist($observed);
    }
    
}