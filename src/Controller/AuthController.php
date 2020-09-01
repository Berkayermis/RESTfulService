<?php
namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthController extends ApiController
{

    /**
     * @param UserRepository $userRepository
     * @return JsonResponse
     * @Route("/users", name="users", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="List all users",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function getUsers(UserRepository $userRepository){
        $data = $userRepository->findAll();
        return $this->response($data);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     * @Route("/users", name="users", methods={"POST"})
     * @SWG\Response(
     *     response=200,
     *     description="Add a new user (Register)",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     * @SWG\Parameter(parameter="user",name="add_user",in="body",required=true,type="array",description="Add a new user (Register)",
     *      @SWG\Schema(
     *          @SWG\Items(
     *              type="integer"
     * )
     * )
     * ),
     * @SWG\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function register(Request $request,EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $request = $this->transformJsonBody($request);
        $username = $request->get('username');
        $password = $request->get('password');

        if (empty($username) || empty($password)){
            return $this->respondValidationError("Invalid Username or Password");
        }

        $user = new User();
        $user->setUsername($username);
        $user->setPassword($encoder->encodePassword($user, $password));
        $em->persist($user);
        $em->flush();
        return $this->respondWithSuccess(sprintf('User %s successfully created', $user->getUsername()));
    }

    /**
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTManager
     * @return JsonResponse
     * @SWG\Response(
     *     response=200,
     *     description="Taking a JSON Web Token",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     * @SWG\Parameter(parameter="JWT",name="JWT",in="body",required=true,type="array",description="Taking a JWT",
     *      @SWG\Schema(
     *          @SWG\Items(
     *              type="string"
     * )
     * )
     * ),
     * @SWG\Tag(name="JWT and Login")
     */
    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager)
    {
        $token = $JWTManager->create($user);
        return new JsonResponse(['token' => $token]);
    }

    /**
     * @param UserRepository $userRepository
     * @param $id
     * @return JsonResponse
     * @Route("/users/{id}", name="users_get", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Fetch an user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function getSingleUser(UserRepository $userRepository, $id){
        $user = $userRepository->find($id);

        if (!$user){
            $data = [
                'status' => 404,
                'errors' => "User not found",
            ];
            return $this->response($data, 404);
        }
        return $this->response((array)$user);
    }

}