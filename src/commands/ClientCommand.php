<?php

namespace Exonos\Mailapi\commands;

use Illuminate\Console\Command;
use Exonos\Mailapi\Models\Client;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'mail:client')]
class ClientCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:client
            {--name= : The name of the client}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a client for issuing access tokens';

    /**
     * Execute the console command.
     *
     * @param  \Exonos\Mailapi\Models\Client  $client
     * @return void
     */
    public function handle(Client $clients)
    {
        $name = $this->option('name') ?: $this->ask(
            'What should we name the access client?',
            config('app.name').' Access Client'
        );

        $client = $clients->create([
            'name' => $name,
            'secret' => Str::random(100),
        ]);

        $this->components->info('Access client created successfully.');

        $this->outputClientDetails($client);
    }

    /**
     * Output the client's ID and secret key.
     *
     * @param  \Exonos\Mailapi\Models\Client  $client
     * @return void
     */
    protected function outputClientDetails(Client $client)
    {
        $this->components->twoColumnDetail('<comment>Client ID</comment>', $client->getKey());
        $this->components->twoColumnDetail('<comment>Client secret</comment>', $client->secret);
    }
}
