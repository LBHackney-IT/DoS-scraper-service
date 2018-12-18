# Hackney Directory of Services Scraper Microservice

The scraper microservice pulls in data from external data sources. It is built with a plug-in architecture, so that scapers or crawlers for a variety of external sources can be created.

Once data has been scraped, it should be put into the system [event stream](https://github.com/LBHackney-IT/DoS-event-stream-service) to create or update entries in the [data store](./datastorehttps://github.com/LBHackney-IT/DoS-data-store-service).

Full documentation can be found in the system documentation microsite at https://docs.hc-dos.co.uk/.

Runs in Docker.

## Installation

Installation assumes you have Docker running.

Copy `.env.example` to `.env` for and adapt as required, e.g. for local development, or set system environment variables. 

```bash
$ make up
```

Containers:

- **php** uses PHP 7.2 image from [convivio/php-kafka](https://hub.docker.com/r/convivio/php-kafka/)
- **nginx** uses Nginx 1.15 image [wodby/nginx](https://hub.docker.com/r/wodby/nginx/)
- **mailhog** to catch local mail
- **traefik**


## API docs

API specification documentation is in the [docs/api](./docs/api) directory.
