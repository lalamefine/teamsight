<?php

namespace App\Controller\Configuration;

use App\Abstraction\AbstractCompanyController;
use App\Entity\Feedback360\Question360;
use App\Entity\Feedback360\QuestionTheme;
use App\Entity\Feedback360\Template360;
use App\Repository\Feedback360\Template360Repository;
use App\Repository\Feedback360\ObsProfileRepository;
use App\Repository\Feedback360\QuestionThemeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class Template360Controller extends AbstractCompanyController
{
    #[Route('/cf/t360', name: 'app_conf_templates_360_list', methods:['GET'], defaults: ['selectedTemplateId' => null])]
    #[Route('/cf/t360/{selectedTemplateId}', name: 'app_conf_templates_360')]
    public function index(?string $selectedTemplateId, Request $request, Template360Repository $template360Repository): Response
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
            $selectedTemplate->setUseQuestionTheme(isset($formData['useQuestionTheme']));
            $selectedTemplate->setCompany($this->company);
            $selectedTemplate->setResponses($formData['responses']);
            $this->em->persist($selectedTemplate);
            $this->em->flush();
            $this->addFlash('success', 'Template updated successfully');
        }
        return $this->render('administration/template360/index.html.twig', [
            'newTemplate' => $selectedTemplateId === 'new',
            'selectedTemplate' => $selectedTemplate
        ]);
    }

    #[Route('/cf/t360/{template}/questions', name: 'app_conf_templates_360_question_list', methods:['GET'])]
    public function questionList(Template360 $template): Response
    {
        return $this->render('administration/template360/questionList.html.twig', [
            'questions' => $template->getQuestions(),
            'template' => $template,
        ]);
    }
    
    #[Route('/cf/t360/{template}/question/{question360}', name: 'app_conf_templates_360_question', defaults: ['question360' => null])]
    public function question(
        Request $request, Template360 $template, ?Question360 $question360, 
        ObsProfileRepository $obsProfileRepository, QuestionThemeRepository $questionThemeRepository): Response
    {
        if ($request->getMethod() === 'POST') {
            if (!$question360) {
                $question360 = new Question360();
            }
            $form = $request->request->all();
            // dd($form);
            $question360->setLibelle($form['libelle']);

            if (isset($form['thematique'])) {
                $theme = $questionThemeRepository->findBy([
                    'company' => $template->getCompany(),
                    'name' => $form['thematique']
                ]);
                if ($theme) {
                    $question360->setThematique($theme[0]);
                } else {
                    $newTheme = new QuestionTheme();
                    $newTheme->setName($form['thematique']);
                    $newTheme->setCompany($template->getCompany());
                    $this->em->persist($newTheme);
                    $this->em->flush();
                    $question360->setThematique($newTheme);
                }
            }
            // $question360->setVerbatim($form['verbatim'] === 'true');
            // $question360->setCustomResponses($form['customResponses']);
            $profiles = $form['profiles'];
            $question360->clearProfiles();
            array_map(function ($profile) use ($question360) {
                $question360->addProfile($profile);
            },$obsProfileRepository->findBy(['id' => $profiles]));
            $template->addQuestion($question360);
            $this->em->persist($question360);
            $this->em->flush();
            return $this->questionList($template);
        }
        return $this->render('administration/template360/questionEditModal.html.twig', [
            'template' => $template,
            'question' => $question360,
        ]);
    }

    // #[Route('/cf/t360/question/{id}', name: 'app_conf_templates_360_question_save', methods:['POST'])]
}
