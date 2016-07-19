<?php

namespace AppBundle\Controller;

use AppBundle\Entity\book;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/books")
 */
class BookController extends Controller
{
    /**
     * @Route("/", name="books", methods={"GET"})
     */
    public function indexAction()
    {
        $books = $this->getDoctrine()->getRepository('AppBundle:book')->findAll();
        return $this->render('book/index.html.twig', array('books' => $books));
    }

    /**
     * @Route("/new", name="books_new", methods={"GET"})
     */
    public function newAction(Request $request) {

        $session = $request->getSession();
        if ($session->get('id') == null) {
            $session->set('back_url', $request->getUri());
            return $this->redirect($this->generateUrl('users_login'));
        }

        return $this->render('book/new.html.twig');
    }

    /**
     * @Route("/create", name="books_create", methods={"POST"})
     */
    public function createAction(Request $request) {

        //从post中取到提交参数
        $attributes = $request->request->get('book');

        $book = new book();
        $book->setName($attributes['name']);
        $book->setAuthor($attributes['author']);
        $book->setPublisher($attributes['publisher']);

        $em = $this->getDoctrine()->getManager();
        $em->persist($book);
        $em->flush();

        $request->getSession()->getFlashBag()->add('success', 'successfully created');
        return $this->redirect($this->generateUrl('books'));
    }

    /**
     * @Route("/{id}/edit", name="books_edit", methods={"GET"})
     */
    public function editAction(Request $request, $id) {

        $session = $request->getSession();
        if ($session->get('id') == null) {
            $session->set('back_url', $request->getUri());
            return $this->redirect($this->generateUrl('users_login'));
        }

        $book = $this->getDoctrine()->getRepository('AppBundle:book')->find($id);
        return $this->render('book/edit.html.twig', array('book' => $book));
    }

    /**
     * @Route("/{id}", name="books_update", methods={"PUT"})
     */
    public function updateAction(Request $request, $id) {

        //从post中取到提交参数
        $attributes = $request->request->get('book');

        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('AppBundle:book')->find($id);
        $book->setName($attributes['name']);
        $book->setAuthor($attributes['author']);
        $book->setPublisher($attributes['publisher']);
        $em->flush();

        $request->getSession()->getFlashBag()->add('success', 'successfully updated');
        return $this->redirect($this->generateUrl('books'));
    }

    /**
     * @Route("/{id}", name="books_delete", methods={"DELETE"})
     */
    public function destroyAction(Request $request, $id) {

        $session = $request->getSession();
        if ($session->get('id') == null) {
            $session->set('back_url', $request->getUri());
            return $this->redirect($this->generateUrl('users_login'));
        }

        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('AppBundle:book')->find($id);
        $em->remove($book);
        $em->flush();

        return $this->redirect($this->generateUrl('books'));
    }
}
