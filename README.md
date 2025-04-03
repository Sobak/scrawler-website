# scrawler.sobak.pl

This repository holds the files for [scrawler.sobak.pl](https://scrawler.sobak.pl) - the website for
the [Scrawler](https://github.com/Sobak/scrawler) project.

## Local setup

This repo features a compatible containers setup using Docker Compose. After cloning the repository, copy
the `.env` file to `.env.local` and set the following variable in it:

```dotenv
SCRAWLER_SOURCES=/var/www/app/var/scrawler/
```

Next, run the following commands to get everything up and running:

```bash
docker-compose up -d

make # enters the app container
composer install

# exit the container (^D or exit)

make node # enters the node container
yarn install
yarn dev

# exit the container (^D or exit)

cd var/
git clone git@github.com:Sobak/scrawler.git
```

After that is done, you should be good to visit [https://127.0.0.1:8000](https://127.0.0.1:8000)
