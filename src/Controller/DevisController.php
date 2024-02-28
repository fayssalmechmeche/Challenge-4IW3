<?php

namespace App\Controller;

use App\Entity\DepositStatus;
use App\Entity\Devis;
use App\Entity\Society;
use App\Form\DevisType;
use App\Entity\DevisProduct;
use App\Repository\DevisRepository;
use App\Repository\FormulaRepository;
use App\Repository\ProductRepository;
use App\Service\Excel\DevisExcelService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Dompdf\Dompdf;
use Dompdf\Options;


#[Route('/devis')]
#[IsGranted('ROLE_SOCIETY')]
class DevisController extends AbstractController
{
  private CsrfTokenManagerInterface $csrfTokenManager;

  public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
  {
    $this->csrfTokenManager = $csrfTokenManager;
  }

  #[Route('/', name: 'app_devis_index', methods: ['GET'])]
  public function index(DevisRepository $devisRepository, EntityManagerInterface $entityManager): Response
  {
    $society = $this->getSociety(); // Récupère l'utilisateur connecté


    if ($society) {
      $devis = $devisRepository->findBy(['society' => $society]);
    } else {
      $devis = []; // Si aucun utilisateur n'est connecté, aucun devis n'est retourné
    }

    $csrfToken = $this->csrfTokenManager->getToken('delete_devis')->getValue();

      $devisRepository = $entityManager->getRepository(Devis::class);
      $allDevis = $devisRepository->findAll();

      // Met à jour le statut de paiement si nécessaire
      foreach ($allDevis as $devis) {
          $devis->updatePaymentStatusBasedOnValidity();
      }

      // Sauvegarde les modifications dans la base de données
      $entityManager->flush();

    return $this->render('devis/index.html.twig', [
      'devis' => $devis,
      'csrf_token' => $csrfToken,
    ]);
  }

  #[Route('/api', name: 'api_devis_index', methods: ['GET'])]
  public function apiIndex(DevisRepository $devisRepository): Response
  {
    $society = $this->getSociety(); // Récupère l'utilisateur connecté

    if ($society) {
      $devis = $devisRepository->findBy(['society' => $society]);
    } else {
      return $this->json([]); // Retourne une réponse vide si aucun utilisateur n'est connecté
    }

    $data = [];
    foreach ($devis as $devi) {
      $customer = $devi->getCustomer();
      $customerName = '';

      if ($customer) {
        $customerName = $customer->getNameSociety() ?: $customer->getName() . ' ' . $customer->getLastName();
      }

      $data[] = [
        'id' => $devi->getId(),
        'devisNumber' => $devi->getDevisNumber(),
        'totalDuePrice' => $devi->getTotalDuePrice(),
        'paymentStatus' => $devi->getPaymentStatus() ? $devi->getPaymentStatus()->value : '',
        'createdAt' => $devi->getCreatedAt() ? $devi->getCreatedAt()->format('Y-m-d') : '',
        'customer' => $customerName,
      ];
    }

    return $this->json($data);
  }


  #[Route('/new', name: 'app_devis_new', methods: ['GET', 'POST'])]
  public function new(Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepository, FormulaRepository $formulaRepository, DevisRepository $devisRepository): Response
  {
    $devis = new Devis();
    $society = $this->getSociety();
    $devis->setSociety($society);
    $lastDevisNumber = $devisRepository->findLastDevisNumberForSociety($society);
    $newDevisNumber = $this->generateNewDevisNumber($lastDevisNumber);

    $societyName = $society ? $society->getName() : '';
    $societyEmail = $society ? $society->getEmail() : '';
    $societyPhone = $society ? $society->getPhone() : '';
    $societyAdress = $society ? $society->getAddress() : '';
    $products = $productRepository->findBy(['society' => $society]);
    $formulas = $formulaRepository->findBy(['society' => $society]);

    $form = $this->createForm(DevisType::class, $devis, [
      'society' => $society,
    ]);
    $form->get('devisNumber')->setData($newDevisNumber);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $devis->setDevisNumber($newDevisNumber);
      if (null !== $devis->getDepositPercentage()) {
        $devis->setDepositStatus(DepositStatus::Prevu);
      } else {
        $devis->setDepositStatus(DepositStatus::NonExistant);
      }
      $devisProductsJson = $request->request->get('devisProductsJson');
      if ($devisProductsJson) {
        $devisProductsData = json_decode($devisProductsJson, true);

        foreach ($devisProductsData as $productData) {
          $product = $productRepository->find($productData['product']);
          if ($product) {
            $devisProduct = new DevisProduct();
            $devisProduct->setProduct($product);
            $devisProduct->setQuantity($productData['quantity']);
            $devisProduct->setPrice($product->getPrice()); // Définir le prix

            $devis->addDevisProduct($devisProduct);
          }
        }
      }
      //dd($devis);
        $entityManager->persist($devis);
        $entityManager->flush();
        $this->addFlash('success', 'Le devis a bien été crée.');
        return $this->redirectToRoute('app_devis_index', [], Response::HTTP_SEE_OTHER);

    }elseif ($form->isSubmitted() && !$form->isValid()) {
        // Nouvelle condition pour gérer les soumissions de formulaire non valides
        $this->addFlash('error', 'Le formulaire contient des erreurs, veuillez vérifier vos informations.');
        return $this->redirectToRoute('app_devis_index');
    }
    return $this->render('devis/new.html.twig', [
      'devis' => $devis,
      'form' => $form->createView(),
      'userEmail' => $societyEmail,
      'userName' => $societyName,
        'userPhone' => $societyPhone,
        'userAddress' => $societyAdress,
      'products' => $products,
      'formulas' => $formulas,
      'devisNumber' => $newDevisNumber,
    ]);
  }



  #[Route('/{id}/show', name: 'app_devis_show', methods: ['GET'])]
  public function show(Devis $devi): Response
  {
    $society = $this->getSociety();
    $societyEmail = $society ? $society->getEmail() : '';

    // Traitement des produits
    $productsCollection = $devi->getDevisProducts();
    $productsCollection->initialize();

    $productsArray = [];

    foreach ($productsCollection as $devisProduct) {
      $product = $devisProduct->getProduct();

      $productsArray[] = [
        'id' => $devisProduct->getId(),
        'name' => $devisProduct->getProduct()->getName(),
        'quantity' => $devisProduct->getQuantity(),
        'price' => $devisProduct->getPrice(),
      ];
    }

    // Traitement des formules
    $formulasCollection = $devi->getDevisFormulas();
    $formulasCollection->initialize();

    $formulasArray = [];
    foreach ($formulasCollection as $devisFormula) {
      $formula = $devisFormula->getFormula();
      $formulasArray[] = [
        'id' => $devisFormula->getId(),
        'name' => $devisFormula->getFormula()->getName(),
        'quantity' => $devisFormula->getQuantity(),
        'price' => $devisFormula->getPrice(),
      ];
    }

    return $this->render('devis/show.html.twig', [
      'devi' => $devi,
      'userEmail' => $societyEmail,
      'products' => $productsArray,
      'formulas' => $formulasArray,
    ]);
  }


  #[Route('/product/{id}/price', name: 'api_product_price', methods: ['GET'])]
  public function getProductPrice($id, ProductRepository $productRepository): JsonResponse
  {
    $product = $productRepository->find($id);
    if (!$product) {
      return new JsonResponse(['error' => 'Produit non trouvé'], 404);
    }
    return new JsonResponse(['price' => $product->getPrice()]);
  }

  #[Route('/formula/{id}/price', name: 'api_formula_price', methods: ['GET'])]
  public function getFormulaPrice($id, FormulaRepository $formulaRepository): JsonResponse
  {
    $formula = $formulaRepository->find($id);
    if (!$formula) {
      return new JsonResponse(['error' => 'Formule non trouvée'], 404);
    }
    return new JsonResponse(['price' => $formula->getPrice()]);
  }

  #[Route('/{id}/edit', name: 'app_devis_edit', methods: ['GET', 'POST'])]
  public function edit(Request $request, Devis $devi, EntityManagerInterface $entityManager, ProductRepository $productRepository, FormulaRepository $formulaRepository): Response
  {
    $society = $this->getSociety();

      $societyName = $society ? $society->getName() : '';
      $societyEmail = $society ? $society->getEmail() : '';
      $societyPhone = $society ? $society->getPhone() : '';
      $societyAdress = $society ? $society->getAddress() : '';

    $products = $productRepository->findBy(['society' => $society]);
    $formulas = $formulaRepository->findBy(['society' => $society]);

    $clientId = $devi->getCustomer()?->getId();

    // Convertir les DevisProducts en tableau
    $devisProductsArray = [];
    foreach ($devi->getDevisProducts() as $devisProduct) {
      $devisProductsArray[] = [
        'id' => $devisProduct->getProduct()->getId(),
        'name' => $devisProduct->getProduct()->getName(),
        'quantity' => $devisProduct->getQuantity(),
        'price' => $devisProduct->getProduct()->getPrice(),
        // Ajoutez d'autres champs si nécessaire
      ];
    }

    // Convertir les DevisFormulas en tableau
    $devisFormulasArray = [];
    foreach ($devi->getDevisFormulas() as $devisFormula) {
      $devisFormulasArray[] = [
        'id' => $devisFormula->getFormula()->getId(),
        'name' => $devisFormula->getFormula()->getName(),
        'quantity' => $devisFormula->getQuantity(),
        'price' => $devisFormula->getFormula()->getPrice(),
        // Ajoutez d'autres champs si nécessaire
      ];
    }

    // Regrouper les produits et les formules
    $devisItems = [
      'products' => $devisProductsArray,
      'formulas' => $devisFormulasArray,
    ];

    $form = $this->createForm(DevisType::class, $devi, [
      'society' => $society,
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        try {

            $entityManager->flush();
            $this->addFlash('success', 'Le devis a été modifié.');
            return $this->redirectToRoute('app_devis_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            // Logique de gestion des erreurs
            $this->addFlash('error', 'Une erreur est survenue lors de la modification du devis. Veuillez réessayer.');
            return $this->redirectToRoute('app_devis_index');
        }
    }

    return $this->render('devis/edit.html.twig', [
      'devi' => $devi,
      'clientId' => $clientId,
        'userEmail' => $societyEmail,
        'userName' => $societyName,
        'userPhone' => $societyPhone,
        'userAddress' => $societyAdress,
      'form' => $form->createView(),
      'products' => $products,
      'formulas' => $formulas,
      'devisItems' => $devisItems,
    ]);
  }



  #[Route('/{id}', name: 'app_devis_delete', methods: ['POST'])]
  public function delete(Request $request, Devis $devi, EntityManagerInterface $entityManager): Response
  {
    if ($this->isCsrfTokenValid('delete_devis', $request->request->get('_token'))) {
      $entityManager->remove($devi);
      $entityManager->flush();
    }
      $this->addFlash('success', 'Le devis a bien été supprimé.');
    return $this->redirectToRoute('app_devis_index', [], Response::HTTP_SEE_OTHER);
  }

  private function generateNewDevisNumber(?string $lastDevisNumber): string
  {
    $year = date('Y');
    $month = date('m');
    $sequentialNumber = 1;

    if ($lastDevisNumber) {
      // Extrait le numéro séquentiel du dernier numéro de devis et l'incrémente
      $lastParts = explode('-', $lastDevisNumber);
      $lastSequential = (int) end($lastParts);
      $sequentialNumber = $lastSequential + 1;
    }

    return sprintf("%s-%s-%04d", $year, $month, $sequentialNumber);
  }

  #[Route('/{id}/download', name: 'app_devis_download', methods: ['GET'])]
  public function download(Devis $devi): Response
  {
    $productsCollection = $devi->getDevisProducts();
    $productsCollection->initialize();

    $productsArray = [];
    foreach ($productsCollection as $devisProduct) {
      $product = $devisProduct->getProduct();
      $productsArray[] = [
        'id' => $devisProduct->getId(),
        'name' => $product ? $product->getName() : '',
        'quantity' => $devisProduct->getQuantity(),
        'price' => $devisProduct->getPrice(),
      ];
    }

    $formulasCollection = $devi->getDevisFormulas();
    $formulasCollection->initialize();

    $formulasArray = [];
    foreach ($formulasCollection as $devisFormula) {
      $formula = $devisFormula->getFormula();
      $formulasArray[] = [
        'id' => $devisFormula->getId(),
        'name' => $formula ? $formula->getName() : '',
        'quantity' => $devisFormula->getQuantity(),
        'price' => $devisFormula->getPrice(),
      ];
    }
    $tableRows = '';


    foreach ($productsArray as $product) {
      $tableRows .= '<tr>
                    <td>' . htmlspecialchars($product['name']) . '</td>
                    <td>' . htmlspecialchars($product['quantity']) . '</td>
                    <td>€' . htmlspecialchars($product['price']) . '</td>
                  </tr>';
    }

    foreach ($formulasArray as $formula) {
      $tableRows .= '<tr>
                    <td>' . htmlspecialchars($formula['name']) . '</td>
                    <td>' . htmlspecialchars($formula['quantity']) . '</td>
                    <td>€' . htmlspecialchars($formula['price']) . '</td>
                  </tr>';
    }

    //Infos user
    $society = $this->getSociety();
    $societyEmail = $society ? $society->getEmail() : '';
    $societySociety = $society->getSociety()?->getName();
    $societyAdress = $society->getSociety()?->getAddress();
    $societyPhoneNumber = $society->getSociety()?->getPhone();



    //Infos client
    $customerSociety = $devi->getCustomer()?->getNameSociety();
    if ($customerSociety === null) {
      $customerName = $devi->getCustomer()?->getName();
      $customerLastName = $devi->getCustomer()?->getLastName();
      $customerInfo = 'Client: ' . $customerName . ' ' . $customerLastName;
    } else {
      $customerInfo = 'Société: ' . $customerSociety;
    }
    $customerStreetName = $devi->getCustomer()?->getStreetName();
    $customerStreetNumber = $devi->getCustomer()?->getStreetNumber();
    $customerAddress = $customerStreetNumber . ' ' . $customerStreetName;
    $customerPostalCode = $devi->getCustomer()?->getPostalCode();
    $customerCity = $devi->getCustomer()?->getCity();
    $customerPhoneNumber = $devi->getCustomer()?->getPhoneNumber();
    $customerEmail = $devi->getCustomer()?->getEmail();

    //Infos prix devis

    $devisTotalPrice = $devi->getTotalPrice();
    $devisTotalDuePrice = $devi->getTotalDuePrice();
    $devisNumber = $devi->getDevisNumber();



    $devisDate = $devi->getCreatedAt()->format('d/m/Y');

    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);

    $dompdf = new Dompdf($options);


    $html = '
<!DOCTYPE html>
<html>
<head>
<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Roboto", sans-serif;
}

body {
  background-color: #adadad;
}

nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 20px;
  height: 50px;
  background-color: #353535;
}
nav .material-symbols-outlined {
  color: white;
  cursor: pointer;
  font-size: 30px;
  transition: all 0.3s ease-in-out;
}
nav .material-symbols-outlined:hover {
  color: red;
}
nav button {
  background-color: rgb(44, 192, 34);
  border: none;
  padding: 10px 15px;
  cursor: pointer;
  border-radius: 5px;
  transition: all 0.3s ease-in-out;
  font-size: 15px;
  font-weight: bold;
}
nav button:hover {
  background-color: rgb(68, 254, 55);
}

.Generator {
  width: 794px;
  height: 1123px;
  margin: 20px auto;
  padding: 20px;
  border: 1px solid #ddd;
  background-color: #fff;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
.Generator .top {
  height: auto;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}
.Generator .top .society {
  width: 50%;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  flex-direction: column;
}
.Generator .top .society img {
  width: 150px;
  height: 150px;
  border: 1px solid #ddd;
}
.Generator .top .society .society-info {
  margin-top: 15px;
  padding: 5px;
}
.Generator .top .society .society-info h2 {
  font-size: 18px;
  margin-bottom: 10px;
}
.Generator .top .society .society-info p {
  font-size: 15px;
}
.Generator .top .top-r {
  width: 50%;
}
.Generator .top .top-r .devis {
  width: 100%;
  background-color: #d4d4d4;
  padding: 15px;
  border: 1px solid #ddd;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  flex-direction: column;
}
.Generator .top .top-r .devis h1 {
  font-size: 23px;
}
.Generator .top .top-r .devis .devis-info p {
  font-size: 15px;
}
.Generator .top .top-r .client {
  margin: 30px 0 0 0;
  padding: 0 5px;
}
.Generator .top .top-r .client h2 {
  font-size: 18px;
}
.Generator .top .top-r .client .client-info {
  margin-top: 15px;
}
.Generator .top .top-r .client .client-info p {
  font-size: 15px;
}
.Generator .table-container {
  width: 100%;
  margin-top: 75px;
  display: flex;
  justify-content: flex-start;
  align-items: center;
  flex-direction: column;
}
.Generator .table-container .objet {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  align-self: flex-start;
  margin-bottom: 20px;
  margin-left: 5px;
}
.Generator .table-container .objet h2 {
  font-size: 18px;
  font-weight: bold;
}
.Generator .table-container .objet p {
  margin-left: 10px;
  font-size: 18px;
}
.Generator .table-container .mid {
  width: 100%;
  height: auto;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}
.Generator .table-container .mid .condition {
  margin-top: 75px;
  display: flex;
  width: 70%;
  justify-content: flex-start;
  align-items: flex-start;
  flex-direction: column;
}
.Generator .table-container .mid .condition h2 {
  font-size: 18px;
  font-weight: bold;
}
.Generator .table-container .mid .condition p {
  margin-top: 20px;
  font-size: 18px;
}
.Generator .table-container .mid .total {
  width: 35%;
  display: flex;
  justify-content: flex-start;
  align-items: flex-end;
  margin: 50px 5px 0 0;
  flex-direction: column;
}
.Generator .table-container .mid .total .total-sub-container {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  margin: 0;
}
.Generator .table-container .mid .total .total-sub-container h3 {
  font-size: 15px;
  font-weight: bold;
}
.Generator .table-container .mid .total .total-sub-container .total-tab {
  margin-left: 5px;
  padding: 15px;
  width: 150px;
  background-color: #d4d4d4;
}
.Generator .table-container .mid .total .total-sub-container .total-tab p {
  font-size: 18px;
  text-align: right;
}
.Generator .table-container .mid .total .total-sub-container .total-tab.alt {
  background-color: #353535;
}
.Generator .table-container .mid .total .total-sub-container .total-tab.alt p {
  font-size: 20px;
  color: #fff;
}
.Generator .signature {
  margin: 100px 5px 0 auto;
  padding: 10px 0 0 10px;
  width: 200px;
  height: 100px;
  border: 1px solid #ddd;
  background-color: rgb(188, 187, 187);
}
.Generator .bottom {
  display: flex;
  justify-content: flex-start;
  align-items: flex-start;
  flex-direction: column;
}
.Generator .bottom .divider {
  width: 100%;
  height: 2px;
  border-radius: 15px;
  background-color: #cdcdcd;
  margin: 20px 0;
}
.Generator .bottom .bottom-info {
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}
.Generator .bottom .bottom-info p {
  font-size: 13px;
  margin: 5px 0;
  color: #868686;
}

.Generator table {
  width: 95%;
  border-collapse: collapse;
}

.Generator th, .Generator td {
  border: 1px solid black;
  padding: 8px;
  text-align: left;
}

.Generator th {
  background-color: #f2f2f2;
}


</style>
<body>
  <div class="Generator">
      <div class="top">
        <div class="society">
          <div class="logoName">
            <img src="./0d4ae847-6828-4179-892b-35ca46596170.webp" alt="logo" />
          </div>
          <div class="society-info">
            <h2>Ma society Name</h2>
            <p>Adresse: ' . $societyAdress . '</p>
            <p>Code Postal: rajouter</p>
            <p>Ville: rajouter</p>
            <p>Téléphone: ' . $societyPhoneNumber . '</p>
            <p>Mail : ' . $societyEmail . '</p>
          </div>
        </div>
        <div class="top-r">
          <div class="devis">
            <h1>Devis</h1>
            <div class="devis-info">
              <p>Numéro de devis:  ' . $devisNumber . '</p>
              <p>Date: ' . $devisDate . '</p>
              <p>Validité du devis: 3 Mois</p>
            </div>
          </div>
          <div class="client">
            <h2>Information Client</h2>
            <div class="client-info">
              <p>' . $customerInfo . '</p>
              <p>Adresse: ' . $customerAddress . '</p>
              <p>Code Postal: ' . $customerPostalCode . '</p>
              <p>Ville: ' . $customerCity . '</p>
              <p>Téléphone: ' . $customerPhoneNumber . ' </p>
              <p>Mail: ' . $customerEmail . '</p>
            </div>
          </div>
        </div>
      </div>
      <div class="table-container">
        <div class="objet">
          <h2>Objet:</h2>
          <p id="commande-title">Prise en charge des repas dun marriage</p>
        </div>
        <div id="wrapper">
        <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Quantité</th>
                <th>Prix</th>
            </tr>
        </thead>
        <tbody>
            ' . $tableRows . '
        </tbody>
    </table>
</div>
        <div class="mid">
          <div class="condition">
            <h2>Conditions de paiement:</h2>
            <p>
    Paiement dun acompte de 30% du prix total TTC à la commande 70% à
              la livraison
            </p>
          </div>
          <div class="total">
            <div class="total-sub-container">
              <h3>Total HT:</h3>
              <div class="total-tab"><p>' . $devisTotalPrice . ' €</p></div>
            </div>
            <div class="total-sub-container">
              <h3>TVA (20%):</h3>
              <div class="total-tab"><p>20%</p></div>
            </div>
            <div class="total-sub-container">
              <h3>Total TTC:</h3>
              <div class="total-tab alt"><p>' . $devisTotalDuePrice . ' €</p></div>
            </div>
          </div>
        </div>
      </div>
      <div class="signature">
        <p>Signature</p>
      </div>
      <div class="bottom">
        <div class="divider"></div>
        <div class="bottom-info">
          <p>SARL Fourchette</p>
          <p>Capital de 1000 €</p>
          <p>RCS:12345</p>
          <p>Code APE: AZERTY12345</p>
          <p>N° TVA: FR3353215643668909</p>
        </div>
      </div>
    </div>
</body>
</html>';


    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $pdfOutput = $dompdf->output();
    $response = new Response($pdfOutput);
    $response->headers->set('Content-Type', 'application/pdf');

    // Vous pouvez personnaliser le nom du fichier PDF ici
    $filename = 'devis-vide.pdf';
    $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

    return $response;
  }


  #[Route('/generate/xlsx', name: 'app_devis_generate_xlsx', methods: ['POST'])]
  public function generateXlsx(DevisExcelService $DevisExcelService, Request $request, EntityManagerInterface $entityManager): Response
  {
    $devis = $entityManager->getRepository(Devis::class)->findBy(['society' => $this->getSociety()]);

    if ($request->isXmlHttpRequest()) {
      return  $DevisExcelService->generateXlsx($devis);
    } else {
      return $this->json([
        'code' => 401,
        'message' => 'Requête non autorisée !',
      ], 401);
    }
  }

  #[Route('/generate/pdf', name: 'app_devis_generate_pdf', methods: ['POST'])]
  public function generatePdf(DevisExcelService $DevisExcelService, Request $request, EntityManagerInterface $entityManager): Response
  {
    $devis = $entityManager->getRepository(Devis::class)->findBy(['society' => $this->getSociety()]);
    if ($request->isXmlHttpRequest()) {
      return  $DevisExcelService->generatePDF($devis);
    } else {
      return $this->json([
        'code' => 401,
        'message' => 'Requête non autorisée !',
      ], 401);
    }
  }

    public function getSociety()
    {
        // Exemple de récupération de l'utilisateur courant et de sa société
        $user = $this->getUser(); // Supposons que `getUser()` retourne l'utilisateur courant
        if ($user) {
            return $user->getSociety(); // Supposons que l'utilisateur a une méthode `getSociety()`
        }
        return null; // ou gérer autrement si l'utilisateur n'est pas connecté ou n'a pas de société
    }
}
