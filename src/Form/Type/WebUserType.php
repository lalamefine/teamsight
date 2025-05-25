<?php 
namespace App\Form\Type;

use App\Entity\Company;
use App\Entity\WebUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class WebUserType extends AbstractType
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
            'data_class' => WebUser::class,
        ]);
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
            ])
            ->add('email', TextType::class, [
                'label' => 'Email (identifiant de connexion et destinataire des notifications de ce compte)',
                'required' => true,
            ]);
        if($this->forCompany->getConfig()->getAgtIdType() === "company") {
            $builder->add('companyInternalID', TextType::class, [
                'label' => 'Identifiant interne de la société',
                'required' => false,
            ]);
        }
        $availableRoles = [
            "ROLE_ENT_ADMIN" => "Administrateur de l'entreprise",
            "ROLE_ENT_USER_MANAGER" => "Gestionnaire des utilisateurs de l'entreprise",
        ];
        if($this->forCompany->getConfig()->isQuestPerc()) {
            $availableRoles["ROLE_T_ENSURV_MAN"] = "Gestion module sondage";
        }
        if($this->forCompany->getConfig()->isQuestComp()) {
            $availableRoles["ROLE_T_REFCOM_MAN"] = "Gestion module compétences";
        }
        if($this->forCompany->getConfig()->isQuestEA()) {
            $availableRoles["ROLE_T_EA_MAN"] = "Gestion module entretien annuel";
        }
        if($this->forCompany->getConfig()->isQuestFdb360()) {
            $availableRoles["ROLE_T_360_MAN"] = "Gestion module Feedback 360";
        }
        $availableRoles = array_filter($availableRoles, function($role) {
            return $this->security->isGranted($role);
        }, ARRAY_FILTER_USE_KEY);
        
        $builder
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles additionnels',
                'choices' => array_flip($availableRoles),
                'multiple' => true,
                'expanded' => true,
                'constraints' => [
                    new Choice([
                        'choices' => array_keys($availableRoles),
                        'multiple' => true,
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
            ]);
    }
}