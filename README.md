# Hackney Directory of Services Scraper Microservice

The scraper microservice pulls in data from external data sources. It is built with a plug-in architecture, so that scapers or crawlers for a variety of external sources can be created.

Once data has been scraped, it should be put into the system [event stream](./eventstream) to create or update entries in the [data store](./datastore).

Full documentation can be found in the system documentation microsite at https://lbhackney-it.github.io/lbhc-docs/.

## API docs

API specification documentation is in the [docs/api](./docs/api) directory.
