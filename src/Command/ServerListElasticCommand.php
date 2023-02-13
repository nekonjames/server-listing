<?php

namespace App\Command;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:server-list-elastic',
    description: 'Add a short description for your command',
)]
class ServerListElasticCommand extends Command
{
    public function __construct(private readonly Client $client)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->info('Importing Data from Excel.....');

        $this->importData();

        $io->info('Import completed...');

        return Command::SUCCESS;
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws Exception
     * @throws MissingParameterException
     */
    private function importData(): void
    {
        if ($this->client->indices()->exists(['index' => 'server-list'])->asBool()){
            $this->client->indices()->delete(['index' => 'server-list']);
        }

        $arrayIndex = [];
        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load('/var/www/app/import/list.xlsx');

        foreach ($spreadsheet->getActiveSheet()->toArray() as $rowNumber => $server) {
            if (0 === $rowNumber) {
                continue;
            }

            list($model, $ram, $hdd, $location, $price) = $server;
            $storage = explode(str_contains(explode('x', $hdd)[1], 'TB') ? 'TB' : 'GB', explode('x', $hdd)[1]);

            $doc = array_merge(
                $arrayIndex,
                [
                    'index' => 'server-list',
                    'id' => $rowNumber,
                    'body' => [
                        'model' => $model,
                        'ram' => $ram,
                        'hdd' => $hdd,
                        'location' => $location,
                        'price' => $price,
                        'storage' => (strlen($storage[0]) > 2 ? $storage[0] : $storage[0] * 1000)
                    ]
                ]
            );
            $this->client->index($doc);
        }

    }
}
