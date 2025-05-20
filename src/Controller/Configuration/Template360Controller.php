<?php

namespace App\Controller\Configuration;

use App\Entity\Question360;
use App\Entity\Template360;
use App\Entity\WebUser;
use App\Repository\ObsProfileRepository;
use App\Repository\Template360Repository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class Template360Controller extends AbstractController
{
    #[Route('/cf/t360', name: 'app_conf_templates_360_list', methods:['GET'], defaults: ['selectedTemplateId' => null])]
    #[Route('/cf/t360/{selectedTemplateId}', name: 'app_conf_templates_360')]
    public function index(?string $selectedTemplateId, Request $request, Template360Repository $template360Repository, #[CurrentUser] WebUser $user, EntityManagerInterface $em): Response
    {
        /** @var Template360|null */
        $selectedTemplate = null;
        if ($selectedTemplateId && $selectedTemplateId !== 'new') {
            $selectedTemplate = $template360Repository->find($selectedTemplateId);
            if (!$selectedTemplate) {
                throw $this->createNotFoundException('Template not found');
            }
        }
        if ($request->isMethod('POST')) {
            if ($selectedTemplateId === 'new') {
                $selectedTemplate = new Template360();
            }
            $formData = $request->request->all();

            $selectedTemplate->setName($formData['name']);
            $selectedTemplate->setDescription($formData['description']);
            $selectedTemplate->setCompany($user->getCompany());
            $selectedTemplate->setResponses($formData['responses']);
            $em->persist($selectedTemplate);
            $em->flush();
            $this->addFlash('success', 'Template updated successfully');
        }
        return $this->render('configuration/template360/index.html.twig', [
            'newTemplate' => $selectedTemplateId === 'new',
            'selectedTemplate' => $selectedTemplate
        ]);
    }

    #[Route('/cf/t360/{template}/questions', name: 'app_conf_templates_360_question_list', methods:['GET'])]
    public function questionList(Template360 $template): Response
    {
        return $this->render('configuration/template360/questionList.html.twig', [
            'questions' => $template->getQuestions(),
            'template' => $template,
        ]);
    }
    
    #[Route('/cf/t360/{template}/question/{question360}', name: 'app_conf_templates_360_question', defaults: ['question360' => null])]
    public function question(Request $request, Template360 $template, ?Question360 $question360, EntityManagerInterface $em, ObsProfileRepository $obsProfileRepository): Response
    {
        if ($request->getMethod() === 'POST') {
            if (!$question360) {
                $question360 = new Question360();
            }
            $form = $request->request->all();
            // dd($form);
            $question360->setLibelle($form['libelle']);
            // $question360->setVerbatim($form['verbatim'] === 'true');
            // $question360->setCustomResponses($form['customResponses']);
            $profiles = $form['profiles'];
            $question360->clearProfiles();
            array_map(function ($profile) use ($question360) {
                $question360->addProfile($profile);
            },$obsProfileRepository->findBy(['id' => $profiles]));
            $template->addQuestion($question360);
            $em->persist($question360);
            $em->flush();
            return $this->questionList($template);
        }
        return $this->render('configuration/template360/questionEditModal.html.twig', [
            'template' => $template,
            'question' => $question360,
        ]);
    }

    // #[Route('/cf/t360/question/{id}', name: 'app_conf_templates_360_question_save', methods:['POST'])]
}
