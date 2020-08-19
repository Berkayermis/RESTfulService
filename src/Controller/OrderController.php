<?php
namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Controller\AuthController;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class OrderController
 * @package App\Controller
 * @Route("/api", name="order_api")
 */
class OrderController extends AbstractController
{
    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserRepository $userRepository
     * @param $userId
     * @return JsonResponse
     * @Route("/users/{userId}/orders", name="orders_add", methods={"POST"})
     */
    public function addOrder(Request $request, EntityManagerInterface $em,UserRepository $userRepository,$userId)
    {
        $user = $userRepository->find($userId);

        try{
            $request = $this->transformJsonBody($request);

            if (!$request || !$request->get('order_code') || !$request->request->get('product_id') || !$request->request->get('quantity') || !$request->request->get('address') || !$request->request->get('shipping_date')){
                throw new Exception();
            }
            else if (!$user) {
                $data = [
                    'status' => 404,
                    'errors' => "User not found",
                ];
                return $this->response($data, 404);
            }


            $order = new Order();
            $order->setOrderCode($request->get('order_code'));
            $order->setProductId($request->get('product_id'));
            $order->setQuantity($request->get('quantity'));
            $order->setAddress($request->get('address'));
            $order->setShippingDate($request->get('shipping_date'));
            $em->persist($order);
            $em->flush();


            $data = [
                'status' => 200,
                'success' => "Order added successfully",
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
     * @param OrderRepository $orderRepository
     * @param UserRepository $userRepository
     * @param $userId
     * @param $orderId
     * @return JsonResponse
     * @Route("/users/{userId}/orders{orderId}", name="orders_get", methods={"GET"})
     */
    public function getOrder(OrderRepository $orderRepository,UserRepository $userRepository, $userId,$orderId)
    {
        $order = $orderRepository->find($orderId);
        $user = $userRepository->find($userId);

        if (!$order) {
            $data = [
                'status' => 404,
                'errors' => "Order not found",
            ];
            return $this->response((array)$order);
        }

        else
            if (!$user) {
                $data = [
                    'status' => 404,
                    'errors' => "User not found",
                ];

                return $this->response($data, 404);
            }
            return $this->response((array)$user);
        }



    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param OrderRepository $orderRepository
     * @param $id
     * @param $idTwo
     * @return JsonResponse
     * @Route("/users/{id}/orders/{idTwo}", name="orders_put", methods={"PUT"})
     */
    public function updateOrder(Request $request, EntityManagerInterface $entityManager, OrderRepository $orderRepository, $id,$idTwo){



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

            if (!$request || !$request->get('orderCode') || !$request->request->get('productId')){
                throw new Exception();
            }

            $order->setOrderCode($request->get('orderCode'));
            $order->setProductId($request->get('productId'));
            $order->setQuantity($request->get('quantity'));
            $order->setAddress($request->get('address'));
            $order->setShippingDate($request->get('shippingDate'));
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

    protected function transformJsonBody(\Symfony\Component\HttpFoundation\Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }
}