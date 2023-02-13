<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\ServerDto;
use App\Form\ServerType;
use Elastic\Elasticsearch\Client;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServerListController extends AbstractController
{
    #[Route('/server/list', name: 'server_index', methods: ['GET', 'POST'])]
    public function index(Request $request, Client $client, PaginatorInterface $paginator): Response
    {
        $response = $client->search([
            'index' => 'server-list',
            'body'  => [
                'size' => 500
            ]
        ]);

        $form = $this->createForm(ServerType::class, $dto = new ServerDto());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $query = $this->processFilter($dto);
            $response = $client->search([
                'index' => 'server-list',
                'body'  => [
                    'size' => 500,
                    'query' => [
                        'query_string' => [
                            'query' => $query
                        ]
                    ]
                ]
            ]);
        }

        return $this->render('/server/index.html.twig', [
            'form' => $form,
            'data' => $paginator->paginate(
                $response['hits']['hits'],
                $request->query->getInt('page', 1),
                100
            )
        ]);
    }

    private function processFilter(ServerDto $dto): string
    {
        $query = '(storage:<='.$dto->storage.')';
        $query .= 'all' !== $dto->location ? ' AND (location:'.$dto->location.'*)' : '';
        $query .= 'all' !== $dto->diskType ? ' AND (hdd:*'.$dto->diskType.'*)' : '';

        foreach ($dto->ram as $index => $ram) {
            if (0 === $index) {
                $query .= $index === array_key_last($dto->ram) ? 'AND (ram:'.$ram.'*)' : 'AND ((ram:'.$ram.'*)';
                continue;
            }
            $query .= ' OR (ram:'.$ram.'*)';
            $query .= $index === array_key_last($dto->ram) ? ')' : '';
        }

        return $query;
    }
}
