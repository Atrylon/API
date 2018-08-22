<?php
/**
 * Created by PhpStorm.
 * User: beren
 * Date: 20/08/2018
 * Time: 11:16
 */

namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UsersController extends FOSRestController
{

    private $userRepository;
    private $em;
    private $validationErrors;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $em,
ConstraintViolationListInterface $validationErrors)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
        $this->validationErrors = $validationErrors;
    }

    /**
     * @Rest\View(serializerGroups={"user"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the users list"
     * )
     * @SWG\Tag(name="user")
     */
    public function getUsersAction(){
        $users = $this->userRepository->findAll();
        return $this->view($users);
    }

    /**
     * @Rest\View(serializerGroups={"user"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the user based on his Id"
     * )
     * @SWG\Tag(name="user")
     */
    public function getUserAction(User $user){
        return $this->view($user);
    }

    /**
     * @Rest\Post("/users")
     * @ParamConverter("user", converter="fos_rest.request_body")
     * @Rest\View(serializerGroups={"user"})
     * @SWG\Response(
     *     response=200,
     *     description="Create a user from a json file"
     * )
     * @SWG\Tag(name="user")
     */
    public function postUsersAction(User $user, ConstraintViolationListInterface $validationErrors){

        if($validationErrors->count() > 0){
            $error = [];
            /** @var  ConstraintViolation $constraintViolation */
            foreach ($validationErrors as $constraintViolation){
                $message= $constraintViolation->getMessage();
                $propertyPath = $constraintViolation->getPropertyPath();
                array_push($error, $message, $propertyPath);
            }
            return json_encode($error);
        }
        else{
            $this->em->persist($user);
            $this->em->flush();
            return $this->view($user);
        }
    }

    /**
     * @Rest\View(serializerGroups={"user"})
     * @SWG\Response(
     *     response=200,
     *     description="Modify the user data based on his Id"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Modify user's data"
     * )
     * @SWG\Tag(name="user")
     */
    public function putUserAction(Request $request, int $id, ValidatorInterface $validator){
        $user = $this->userRepository->find($id);
        $firstname = $request->get('firstname');
        $lastname = $request->get('lastname');
        $email = $request->get('email');
        $birthday = $request->get('birthday');

        if ($this->getUser() === $user or in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            if (isset($firstname)) {
                $user->setFirstname($firstname);
            }
            if (isset($lastname)) {
                $user->setLastname($lastname);
            }
            if (isset($email)) {
                $user->setEmail($email);
            }
            if (isset($birthday)) {
                $user->setBirthday($birthday);
            }


            $validationErrors = $validator->validate($user);
            $this->em->persist($user);
            $error=[];
            foreach ($validationErrors as $constraintViolation){
                $message= $constraintViolation->getMessage();
                $propertyPath = $constraintViolation->getPropertyPath();
                array_push($error, $message, $propertyPath);
            }
            if(sizeof($error)>0) {
                return json_encode($error);
            }
            $this->em->flush();
        }

        return $this->view($user);

    }

    /**
     * @Rest\View(serializerGroups={"user"})
     * @SWG\Response(
     *     response=200,
     *     description="Delete the user based on his Id"
     * )
     * @SWG\Tag(name="user")
     */
    public function deleteUserAction(int $id){

        $user = $this->userRepository->find($id);

        $articles = $user->getArticles();
        foreach($articles as $article){
            $article->setUser(null);
        }

        if ($this->getUser() === $user or in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            $this->em->remove($user);
            $this->em->flush();
        }
    }

}