<?php
/**
 * Created by PhpStorm.
 * User: beren
 * Date: 20/08/2018
 * Time: 16:30
 */

namespace App\Controller;


use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;

class ArticlesController extends FOSRestController
{
    private $articleRepository;
    private $userRepository;
    private $em;

    public function __construct(ArticleRepository $article, UserRepository $userRepository,
                                EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->articleRepository = $article;
        $this->em = $em;
    }

    /**
     * @Rest\View(serializerGroups={"article"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the articles list"
     * )
     * @SWG\Tag(name="article")
     */
    public function getArticlesAction(){
        $articles = $this->articleRepository->findAll();
        return $this->view($articles);
    }

    /**
     * @Rest\View(serializerGroups={"article"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the articles list of a user based on his Id"
     * )
     * @SWG\Tag(name="article")
     */
    public function getUserArticlesAction(int $id){
        $user = $this->userRepository->find($id);
        $article = $user->getArticles();
        return $this->view($article);
    }

    /**
     * @Rest\View(serializerGroups={"article"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the article based on it Id"
     * )
     * @SWG\Tag(name="article")
     */
    public function getArticleAction(Article $article){
        return $this->view($article);
    }

    /**
     * @Rest\Post("/articles")
     * @ParamConverter("article", converter="fos_rest.request_body")
     * @Rest\View(serializerGroups={"article"})
     * @SWG\Response(
     *     response=200,
     *     description="Create an article with a json file"
     * )
     * @SWG\Tag(name="article")
     */
    public function postArticlesAction(Article $article){
        $this->em->persist($article);
        $this->em->flush();
        return $this->view($article);
    }

    /**
     * @Rest\View(serializerGroups={"article"})
     * @SWG\Response(
     *     response=200,
     *     description="Modify the article's data based on it Id"
     * )
     * @SWG\Tag(name="article")
     */
    public function putArticleAction(Request $request, int $id){
        $article = $this->articleRepository->find($id);
        $name = $request->get('name');
        $description = $request->get('$description');
        $user_id = $request->get('$user_id');

        if ($this->getUser() === $article->getUser() or in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            if(isset($name)){
                $article->setName($name);
            }
            if(isset($lastname)){
                $article->setDescription($description);
            }
            if(isset($user_id)){
                $article->setUser($user_id);
            }

            $this->em->persist($article);
            $this->em->flush();
        }

        return $this->view($article);
    }

    /**
     * @Rest\View(serializerGroups={"article"})
     * @SWG\Response(
     *     response=200,
     *     description="Delete the article based on it Id"
     * )
     * @SWG\Tag(name="article")
     */
    public function deleteArticleAction(int $id){

        $article = $this->articleRepository->find($id);

        if ($this->getUser()->getArticle() === $article or in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            $this->em->remove($article);
            $this->em->flush();
        }
    }

}