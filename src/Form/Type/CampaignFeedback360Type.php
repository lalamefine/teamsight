<?php 
namespace App\Form\Type;

use App\Entity\Company;
use App\Entity\Feedback360\CampaignFeedback360;
use App\Entity\WebUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampaignFeedback360Type extends AbstractType
{
    private ?Company $forCompany;
    public function __construct(private Security $security){
        /** @var WebUser|null */
        $u = $this->security->getUser();
        $this->forCompany = $u ? $u->getCompany() : null;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CampaignFeedback360::class,
        ]);
    }
        
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // dd($options);
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la campagne',
                'required' => true,
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message présenté en-tête des questionnaires',
                'attr' => ['rows' => 3, 'readonly' => $options['data']?->isStateOrAfter(CampaignFeedback360::STATE_ANS_OPEN) ?? false],
                'required' => true
            ])
            ->add('beginAt', DateType::class, [
                'label' => 'Date de début',
                'required' => false,
                'row_attr' => ['class' => 'col'],
            ])
            ->add('endAt', DateType::class, [
                'label' => 'Date de fin',
                'required' => false,
                'row_attr' => ['class' => 'col'],
            ]);
        
        if ($this->forCompany->getConfig()->isFdb360askPanelToEvalue()) {
            $builder
                ->add('panelProposalOpenedAt', DateType::class, [
                    'label' => 'Date d\'ouverture de la proposition de panel par les évalués',
                    'required' => false,
                    'row_attr' => ['class' => 'col'],
                ])
                ->add('panelProposalEvalueClosedAt', DateType::class, [
                    'label' => 'Date de clôture de la proposition de panel par les évalués',
                    'required' => false,
                    'row_attr' => ['class' => 'col'],
                ]);
        }
        if ($this->forCompany->getConfig()->isFdb360askPanelToHierarchy()) {
            $builder
                ->add('panelProposalHierarchyClosedAt', DateType::class, [
                    'label' => 'Date de clôture de la confirmation de panel par la hiérarchie',
                    'required' => false,
                    'row_attr' => ['class' => 'col'],
                ]);
        }

        $builder->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary float-end wheel'],
            ]);
    }
}