# server-listing - ElasticSearch Demonstration with Symfony

### Docker

> If you don't have Docker installed yet, please follow the instructions to get the **latest** version of [Docker](https://docs.docker.com/engine/install/ubuntu/)
> and [Docker Compose](https://docs.docker.com/compose/install/). Please note the Linux Mint specific instructions in the docker install guide.
> Or check this answer in the [Linux Mint Forums](https://forums.linuxmint.com/viewtopic.php?p=1851409) for a quick solution.

To start your project docker, do the following:
* Use this command for first time setup `docker-compose up -d --build` to build your docker and create the required services as specified in docker-compose.yaml"

Now every other time you want to work on this project:
* Run `docker-compose up -d`"

### Install / Reset

Both for initial install and to re-run when `composer.json` has changed (ex. after a `git pull`)
or in general when you want the database or composer to be reset:

```bash
# this does `composer install` + importing excel file to Elasticsearch
bin/reset