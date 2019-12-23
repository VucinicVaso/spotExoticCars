<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

// entity, resources, form
use App\Entity\Model;
use App\Entity\Brand;
use App\Repository\BrandRepository;
use App\Repository\ModelRepository;

class ModelController extends AbstractController
{
	/**
     * @var BrandRepository
     */
    private $brandRepository;
	/**
     * @var ModelRepository
     */
    private $modelRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(BrandRepository $brandRepository, ModelRepository $modelRepository, EntityManagerInterface $entityManager)
    {
        $this->brandRepository = $brandRepository;
       	$this->modelRepository = $modelRepository;
        $this->entityManager   = $entityManager;
    }

    /**
     * @Route("/model/get/{id}", name="getModels", methods={"GET"})
     */
    public function getModels($id)
    {
        return $this->json([ 'models' => $this->modelRepository->findModelsByBrand($id) ], 200);
    }

    /**
     * @Route("/model/{id}", name="model_show", requirements={"id":"\d+"})
     */
    public function show(Model $model)
    {
        return $this->render('models/show.html.twig', [
            'title' => $model->getTitle(),
            'model' => $model
        ]);
    }

     /**
     * @Route("model/edit/{id}", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
    */
    public function edit(Model $model, Request $request)
    {
    	$data = json_decode($request->getContent(), true);
        $validationErrors = $this->modelRepository->setAndUpdateModel($model, $this->brandRepository->find($data['brand']), $data['title']);

        return !empty(($validationErrors))
        	? $this->json(['errors' => $validationErrors], 400)
        	: $this->json(['success' => 'Model updated successfully!'], 200);
    }

    /**
    * @Route("model/delete/{id}", name="delete_model", methods={"POST"}, requirements={"id":"\d+"})
    * @Security("is_granted('ROLE_ADMIN')")
    */
    public function delete(Model $model)
    {
        $this->entityManager->remove($model);
        $this->entityManager->flush();
        return $this->json(['success' => 'Model deleted successfully.'], 200);
    }

    /**
    * @Route("model/create", methods={"POST"})
    * @Security("is_granted('ROLE_ADMIN')")
    */
    public function create(Request $request)
    {
    	$data = json_decode($request->getContent(), true);
        $validationErrors = $this->modelRepository->setAndCreateModel($data['title'], $this->brandRepository->find($data['brand']), $this->getUser());

        return !empty(($validationErrors))
        	? $this->json(['errors' => $validationErrors], 400)
        	: $this->json(['success' => 'Model created successfully!'], 200);
    }

}
