<?php
namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;


/**
 * Class OrderController
 * @package App\Controller
 * @Route("/api", name="order_api")
 */
class OrderController extends AbstractController
{

    /**
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @return JsonResponse
     * @Route("/orders", name="orders_add", methods={"POST"})
     * @SWG\Response(
     *     response=200,
     *     description="Add a new Order",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Order::class, groups={"full"}))
     *     )
     * )
     * @SWG\Parameter(parameter="order",name="order",in="body",required=true,type="array",description="order info",
     *      @SWG\Schema(
     *          @SWG\Items(
     *              type="integer"
     * )
     * )
     * ),
     * @SWG\Tag(name="orders")
     * @Security(name="Bearer")
     */
    public function addOrder(Request $request,TokenStorageInterface $tokenStorage)
    {
        $user = $tokenStorage->getToken()->getUser();
        $entityManager = $this->getDoctrine()->getManager();

        try{
            $request = $this->transformJsonBody($request);

            if (!$request || !$request->request->get('product_id') || !$request->request->get('quantity') || !$request->request->get('address') ){
                throw new Exception();
            }

            $order = new Order();

            $order->setProductId($request->get('product_id'));
            $order->setQuantity($request->get('quantity'));
            $order->setAddress($request->get('address'));
            $order->setShippingDate($request->get('shipping_date'));
            $order->setUser($user);
            $user->addOrder($order);
            $entityManager->persist($user);
            $entityManager->persist($order);
            $entityManager->flush();

            $data = [
                'status' => 200,
                'success' => "Order added successfully",
            ];
            return $this->response($data);

        }catch (Exception $logger){
            $logger->getMessage();
            return $this->response((array)$logger, 422);
        }
    }


    /**
     * @param OrderRepository $orderRepository
     * @param $id
     * @return JsonResponse
     * @Route("/orders/{id}", name="orders_get", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the orders with ID",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Order::class, groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="orders")
     * @Security(name="Bearer")
     */
    public function getOrder(OrderRepository $orderRepository,$id)
    {
        $order = $orderRepository->find($id);

        if (!$order) {
            $data = [
                'status' => 404,
                'errors' => "Order not found",
            ];
            return $this->response($data,404);
        }
            return $this->response((array)$order);
        }

    /**
     * @Route("/orders", name="all_orders", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the all orders",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Order::class, groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="orders")
     * @Security(name="Bearer")
     * @param OrderRepository $orderRepository
     * @return JsonResponse
     */
    public function getAllOrders(OrderRepository $orderRepository){
        $orders = $orderRepository->findAll();
        return $this->response($orders);

    }


    /**
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @param $id
     * @return JsonResponse
     * @Route("/orders/{id}", name="orders_put", methods={"PUT"})
     * @SWG\Response(
     *     response=200,
     *     description="Update an order",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Order::class, groups={"full"}))
     *     )
     * )
     * @SWG\Parameter(parameter="update_order",name="update_order",in="body",required=true,type="array",description="order updating",
     *      @SWG\Schema(
     *          @SWG\Items(
     *              type="integer"
     *              )
     *          )
     *      )
     * @SWG\Tag(name="orders")
     * @Security(name="Bearer")
     */
    public function updateOrder(Request $request, OrderRepository $orderRepository, $id){

        $entityManager = $this->getDoctrine()->getManager();

        try{
            $order = $orderRepository->find($id);

            if (!$order){
                $data = [
                    'status' => 404,
                    'errors' => "Order not found",
                ];
                return $this->response($data, 404);
            }

            $request = $this->transformJsonBody($request);

            if (!$request || !$request->request->get('product_id') || !$request->request->get('quantity') || !$request->request->get('address') || !$request->request->get('shipping_date')){
                throw new Exception();
            }

            $order->setProductId($request->get('product_id'));
            $order->setQuantity($request->get('quantity'));
            $order->setAddress($request->get('address'));
            $order->setShippingDate($request->get('shipping_date'));
            $entityManager->persist($order);
            $entityManager->flush();

            $data = [
                'status' => 200,
                'errors' => "Order updated successfully",
            ];
            return $this->response($data);

        }catch (Exception $e){
            $data = [
                'status' => 422,
                'errors' => "Order no valid",
            ];
            return $this->response($data, 422);
        }

    }


    /**
     * @param EntityManagerInterface $entityManager
     * @param OrderRepository $orderRepository
     * @param $id
     * @return JsonResponse
     * @Route("/orders/{id}", name="orders_delete", methods={"DELETE"})
     * @SWG\Response(
     *     response=200,
     *     description="Delete the order",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Order::class, groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="orders")
     * @Security(name="Bearer")
     */
    public function deleteOrder(EntityManagerInterface $entityManager, OrderRepository $orderRepository, $id){
        $order = $orderRepository->find($id);

        if (!$order){
            $data = [
                'status' => 404,
                'errors' => "Order not found",
            ];
            return $this->response($data, 404);
        }

        $entityManager->remove($order);
        $entityManager->flush();
        $data = [
            'status' => 200,
            'errors' => "Order deleted successfully",
        ];
        return $this->response($data);
    }


    /**
     * Returns a JSON response
     *
     * @param array $data
     * @param $status
     * @param array $headers
     * @return JsonResponse
     */
    public function response($data, $status = 200, $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }

    protected function transformJsonBody(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }
}