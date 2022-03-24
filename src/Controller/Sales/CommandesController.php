<?php


namespace App\Controller\Sales;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Gestion\Numerator;

class CommandesController extends AbstractController
{
    /**
     * @return array
     * @throws \Exception
     */
    public function facture()
    {
        $em = $this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();
        $adresse = $session->get('adresse');
        $panier = $session->get('panier');
        $commande = [];
        $totalHT = 0;
        $totalTVA = 0;

        $facturation = $em->getRepository('App:Users\Adresses')->find($adresse['facturation']);
        $livraison = $em->getRepository('App:Users\Adresses')->find($adresse['livraison']);
        $produits = $em->getRepository('Products.php')->findArray(array_keys($session->get('panier')));

        foreach($produits as $produit)
        {
            $prixHT = ($produit->getPrix() * $panier[$produit->getId()]);
            $prixTTC = ($produit->getPrix() * $panier[$produit->getId()] / $produit->getTva()->getMultiplicate());
            $totalHT += $prixHT;

            if (!isset($commande['tva']['%'.$produit->getTva()->getValeur()]))
                $commande['tva']['%'.$produit->getTva()->getValeur()] = round($prixTTC - $prixHT,2);
            else
                $commande['tva']['%'.$produit->getTva()->getValeur()] += round($prixTTC - $prixHT,2);

            $totalTVA += round($prixTTC - $prixHT,2);

            $commande['produit'][$produit->getId()] = array('reference' => $produit->getNom(),
                'quantite' => $panier[$produit->getId()],
                'prixHT' => round($produit->getPrix(),2),
                'prixTTC' => round($produit->getPrix() / $produit->getTva()->getMultiplicate(),2));
        }

        $commande['livraison'] = array('prenom' => $livraison->getPrenom(),
            'nom' => $livraison->getNom(),
            'telephone' => $livraison->getTelephone(),
            'adresse' => $livraison->getAdresse(),
            'cp' => $livraison->getCp(),
            'ville' => $livraison->getVille(),
            'pays' => $livraison->getPays(),
            'complement' => $livraison->getComplement());

        $commande['facturation'] = array('prenom' => $facturation->getPrenom(),
            'nom' => $facturation->getNom(),
            'telephone' => $facturation->getTelephone(),
            'adresse' => $facturation->getAdresse(),
            'cp' => $facturation->getCp(),
            'ville' => $facturation->getVille(),
            'pays' => $facturation->getPays(),
            'complement' => $facturation->getComplement());

        $commande['prixHT'] = round($totalHT,2);
        $commande['prixTTC'] = round($totalHT + $totalTVA,2);
        $commande['token'] = bin2hex(random_bytes(20));

        return $commande;
    }

    /**
     * @return Response
     * @throws \Exception
     */
    public function prepareCommande()
    {
        $session = $this->getRequest()->getSession();
        $em = $this->getDoctrine()->getManager();

        if (!$session->has('commande'))
            $commande = new Commandes();
        else
            $commande = $em->getRepository('App:Admin\Orders')->find($session->get('commande'));

        $commande->setDate(new \DateTime());
        $commande->setUtilisateur($this->getUser());
        $commande->setValider(0);
        $commande->setReference(0);
        $commande->setCommande($this->facture());

        if (!$session->has('commande')) {
            $em->persist($commande);
            $session->set('commande',$commande);
        }

        $em->flush();

        return new Response($commande->getId());
    }

    /*
    *   Cette methode remplace l'API banque
    */
    /**
     * @Route("/api/banque/{id}", name="page_validationCommande")
     * @param $id
     * @param Numerator $numerator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function validationCommande($id, Numerator $numerator)
    {
        $em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository('App:Admin\Orders')->find($id);

        if (!$commande || $commande->getValider() == 1)
            throw $this->createNotFoundException('La commande n\'existe pas');

        $commande->setValider(1);
        $commande->setReference($numerator->reference()); //Service
        $em->flush();

        $session = $this->getRequest()->getSession();
        $session->remove('adresse');
        $session->remove('panier');
        $session->remove('commande');

        // ici le mail de validation
        $message = \Swift_Message::newInstance()
            ->setSubject('Validation de votre commande')
            ->setFrom(array('r.benaata@gmail.com' => "Fruits & Légumes"))
            ->setTo($commande->getUtilisateur()->getEmailCanonical())
            ->setCharset('utf-8')
            ->setContentType('text/html')
            ->setBody($this->renderView('Message/SwiftLayout/validationCommande.html.twig',array('utilisateur' => $commande->getUtilisateur())));
        $this->get('mailer')->send($message);


        $this->get('session')->getFlashBag()->add('success','Votre commande est validé avec succès');
        return $this->redirectToRoute('page_factures');
    }
}