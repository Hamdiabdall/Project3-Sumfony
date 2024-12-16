<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

#[Route('/formation')]
class FormationController extends AbstractController
{
    private $formationRepository;
    private $entityManager;

    public function __construct(
        FormationRepository $formationRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->formationRepository = $formationRepository;
        $this->entityManager = $entityManager;
    }

    private function createFormationForm(Formation $formation)
    {
        return $this->createFormBuilder($formation)
            ->add('titre', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('price', NumberType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('duree', NumberType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('begin_at', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG or PNG)',
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/*'
                ]
            ])
            ->getForm();
    }

    #[Route('/', name: 'formation_liste')]
    public function index(): Response
    {
        return $this->render('formation/index.html.twig', [
            'formations' => $this->formationRepository->findAll(),
        ]);
    }

    #[Route('/nouvelle', name: 'formation_nouvelle')]
    public function addF(Request $request, SluggerInterface $slugger): Response
    {
        $formation = new Formation();
        $form = $this->createFormationForm($formation);
        
        return $this->handleFormSubmission(
            $request, 
            $form, 
            $formation, 
            $slugger, 
            'formation/new.html.twig'
        );
    }

    #[Route('/{id}/modifier', name: 'formation_modifier')]
    public function editAll(Request $request, Formation $formation, SluggerInterface $slugger): Response
    {
        $form = $this->createFormationForm($formation);
        
        return $this->handleFormSubmission(
            $request, 
            $form, 
            $formation, 
            $slugger, 
            'formation/edit.html.twig'
        );
    }

    private function handleFormSubmission(
        Request $request, 
        $form, 
        Formation $formation, 
        SluggerInterface $slugger,
        string $template
    ): Response {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleImageUpload($form, $formation, $slugger);

            if (!$formation->getId()) {
                $this->entityManager->persist($formation);
            }
            
            $this->entityManager->flush();

            return $this->redirectToRoute('formation_liste');
        }

        return $this->render($template, [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    private function handleImageUpload($form, Formation $formation, SluggerInterface $slugger): void
    {
        $imageFile = $form->get('image')->getData();

        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('formation_images_directory'),
                    $newFilename
                );
                
                if ($formation->getImage() && $formation->getId()) {
                    $oldImagePath = $this->getParameter('formation_images_directory').'/'.$formation->getImage();
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                
                $formation->setImage($newFilename);
            } catch (FileException $e) {
                // Log error if needed
            }
        }
    }

    #[Route('/{id}/supprimer', name: 'formation_supprimer')]
    public function delete(Request $request, Formation $formation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) {
            if ($formation->getImage()) {
                $imagePath = $this->getParameter('formation_images_directory').'/'.$formation->getImage();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $this->entityManager->remove($formation);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('formation_liste');
    }

    #[Route('/{id}/voir', name: 'formation_voir')]
    public function show(Formation $formation): Response
    {
        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
        ]);
    }
} 