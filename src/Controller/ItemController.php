<?php
namespace App\Controller;

use Symfony\Component\Form\FormInterface;
use FOS\RestBundle\Decoder\DecoderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use App\Entity\Item;
use App\Form\ItemType;
use App\Repository\ItemRepository;
/**
 * Brand controller.
 *
 * @Route("/api")
 */
class ItemController extends FOSRestController
{
    /**
     * Lists all Items.
     * @FOSRest\Get("/items")
     *
     * @return array
     */
    public function getItemsAction(Request $request): Response
    {
        $repository = $this->getDoctrine()->getRepository(Item::class);
        $items = $repository->findAll();

        if(empty($items)) return new JsonResponse(null, Response::HTTP_NO_CONTENT);

        return new JsonResponse($items,  Response::HTTP_OK);
    }

    /**
     * Lists all Items.
     * @FOSRest\Get("/items/available")
     *
     * @return array
     */
    public function getAvailableItemsAction(Request $request): Response
    {
        $repository = $this->getDoctrine()->getRepository(Item::class);
        $items = $repository->findAllAvailable();

        if(empty($items)) return new JsonResponse(null, Response::HTTP_NO_CONTENT);

        return new JsonResponse($items,  Response::HTTP_OK);
    }

    /**
     * Lists all Items.
     * @FOSRest\Get("/items/not-available")
     *
     * @return array
     */
    public function getNotAvailableItemsAction(Request $request): Response
    {
        $repository = $this->getDoctrine()->getRepository(Item::class);
        $items = $repository->findAllNotAvailable();

        if(empty($items)) return new JsonResponse(null, Response::HTTP_NO_CONTENT);

        return new JsonResponse($items,  Response::HTTP_OK);
    }

    /**
     * Lists all Items.
     * @FOSRest\Get("/items/greather-than-amount/{amount}")
     *
     * @return array
     */
    public function getItemsGreaterThanAmountActionAction(Request $request, $amount): Response
    {
        $repository = $this->getDoctrine()->getRepository(Item::class);
        $items = $repository->findAllGreaterThanAmount($amount);

        if(empty($items)) return new JsonResponse(null, Response::HTTP_NO_CONTENT);

        return new JsonResponse($items,  Response::HTTP_OK);
    }

    /**
     * Create Item.
     * @FOSRest\Put("/item")
     *
     * @return array
     */
    public function newItemAction(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ItemType::class, new Item());
        $this->processForm($request, $form);
        if (false === $form->isValid()) {  
            return new JsonResponse(
                [
                    'status' => 'error',
                    'errors' => $this->getFormErrors($form)
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
        $itemFromForm = $form->getData();
        $em->persist($itemFromForm);
        $em->flush();

        return new JsonResponse(
            [
                'id' => $itemFromForm->getId()
            ], 
            JsonResponse::HTTP_CREATED
        );
    }


    /**
     * @Route(
     *     "/item/{id}",
     *     name="update_item",
     *     methods={"PUT"},
     *     requirements={"id"="\d+"}
     * )
     * @param int $id
     *
     * @return JsonResponse
     */
    public function updateItemAction(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(Item::class);
        $existingItem = $repository->find($id);
        if(!empty($existingItem)){
            $form = $this->createForm(ItemType::class, $existingItem);
            $this->processForm($request, $form);
            if (false === $form->isValid()) {  
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'errors' => $this->getFormErrors($form)
                    ],
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }
            $em->flush();
            return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Route(
     *     "/item/{id}",
     *     name="delete_item",
     *     methods={"DELETE"},
     *     requirements={"id"="\d+"}
     * )
     * @param int $id
     *
     * @return JsonResponse
     */
    public function deleteItemAction($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(Item::class);
        $item = $repository->find($id);

        if (!empty($item)) {
            $em->remove($item);
            $em->flush();
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * List all errors of a given bound form.
     *
     * @param FormInterface $form
     *
     * @return void
     */
    private function processForm(Request $request, FormInterface $form): void
    {
        $data = json_decode($request->getContent(), true);
        $clearMissing = $request->getMethod() != 'PATCH';
        $form->submit($data, $clearMissing);
    }

    /**
     * List all errors of a given bound form.
     *
     * @param FormInterface $form
     *
     * @return array
     */
    private function getFormErrors(FormInterface $form): array
    {
        $errors = array();

        // Global
        foreach ($form->getErrors() as $error) {
            $errors[$form->getName()][] = $error->getMessage();
        }

        // Fields
        foreach ($form as $child /** @var Form $child */) {
            if (!$child->isValid()) {
                foreach ($child->getErrors() as $error) {
                    $errors[$child->getName()][] = $error->getMessage();
                }
            }
        }

        return $errors;
    }


}