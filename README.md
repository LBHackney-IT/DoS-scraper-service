# Hackney Directory of Services Scraper Microservice

The scraper microservice pulls in data from external data sources. It is built with a plug-in architecture, so that scapers or crawlers for a variety of external sources can be created.

Once data has been scraped, it should be put into the system [event stream](https://github.com/LBHackney-IT/DoS-event-stream-service) to create or update entries in the [data store](./datastorehttps://github.com/LBHackney-IT/DoS-data-store-service).

Full documentation can be found in the system documentation microsite at https://docs.hc-dos.co.uk/.

## API docs

API specification documentation is in the [docs/api](./docs/api) directory.
