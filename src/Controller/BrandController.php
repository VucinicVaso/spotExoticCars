<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

// entity, repository, form
use App\Entity\Brand;
use App\Repository\BrandRepository;
use App\Repository\ModelRepository;

class BrandController extends AbstractController
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
     * @Route("/brandAndModel", name="brandAndModel")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index()
    {
        return $this->render('brandandmodel/index.html.twig', [
            'title'       => 'BRAND AND MODEL',
            'count_brand' => $this->brandRepository->countBrand(),
            'count_model' => $this->modelRepository->countModel()
        ]);
    }

    /**
     * @Route("/brand/get", name="get_brands", methods={"GET"})
     */
    public function getBrands()
    {
        return $this->json(['brands' => $this->brandRepository->findAll()], 200);
    }

    /**
     * @Route("/brand/{id}", name="brand_show", requirements={"id":"\d+"})
     */
    public function show(Brand $brand)
    {
        return $this->render('brand/show.html.twig', [
            'title' => $brand->getTitle(),
            'brand' => $brand
        ]);
    }

    /**
     * @Route("/brand/edit/{id}", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
    */
    public function edit(Brand $brand, Request $request)
    {
    	$data = json_decode($request->getContent(), true);
        $validationErrors = $this->brandRepository->setAndUpdateBrand($brand, $data['title']);

        return !empty(($validationErrors))
        	? $this->json(['errors' => $validationErrors], 400)
        	: $this->json(['success' => 'Brand updated successfully!'], 200);
    }

    /**
    * @Route("brand/delete/{id}", methods={"POST"}, requirements={"id":"\d+"})
    * @Security("is_granted('ROLE_ADMIN')")
    */
    public function destroy(Brand $brand)
    {
        $this->entityManager->remove($brand);
        $this->entityManager->flush();
        return $this->json(['success' => 'Brand deleted successfully.'], 200);
    }

    /**
    * @Route("brand/create", methods={"POST"})
    * @Security("is_granted('ROLE_ADMIN')")
    */
    public function create(Request $request)
    {
    	$data = json_decode($request->getContent(), true);
        $validationErrors = $this->brandRepository->setAndCreateBrand($data['title'], $this->getUser());

        return !empty(($validationErrors))
        	? $this->json(['errors' => $validationErrors], 400)
        	: $this->json(['success' => 'Brand created successfully!'], 200);
    }

}
