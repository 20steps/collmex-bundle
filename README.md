# 20steps/collmex-bundle (twentystepsCollmexBundle)

## About

The 20steps Collmex Bundle provides a Service-oriented API for Symfony2 applications that need to interact with the Collmex accounting service.

For further information about Collmex goto http://www.collmex.de.

## Features

- [x] Collmex accessible as a configurable Symfony2 service.
- [ ] Complete CRUD API for Collmex.
- [ ] Configurable caching of responses to prevent surpassing rate limit.
- [ ] Provide some derived KPIs.
- [ ] Full documentation and some examples.
- [ ] Prepare for open sourcing of 20steps control.

## Installation

Require the bundle by adding the following entry to the respective section of your composer.json:
```
"20steps/collmex-bundle": "dev-master"
```

Get the bundle via packagist from GitHub by calling:
```
php composer.phar update 20steps/collmex-bundle
```

Register the bundle in your application by adding the following line to the registerBundles() method of your AppKernel.php:  
```
new twentysteps\Bundle\CollmexBundle\twentystepsCollmexBundle()
```

Register services provided by the bundle by adding the following line to the imports section of your config.yml:  
```
- { resource: "@twentystepsCollmexBundle/Resources/config/services.yml" }
```

Define the following properties in your parameters.yml:  
* twentysteps_collmex.url - URL of the Collmex API - normally should point to "https://www.collmex.de".
* twentysteps_collmex.account_id - ID of your account at Collmex. You will need a "Pro" account at collmex use this API.
* twentysteps_collmex.login - The login to use for accessing Collmex. You should create a service account to not interfere with the sessions of your accountants.
* twentysteps_collmex.password - Password of the account.

## Usage

* Get reference to the Collmex service either by adding @twentysteps_collmex.service as a dependency in your service or by  explicitely getting the service from the container during runtime e.g. by calling $this->get('twentysteps_collmex.service') in the action of your controller.
* Call any public function provided by Services/CollmexService.php e.g. getCustomerCount() to get the number of customers listed in Collmex.

## Version

This version is not yet complete or usable.

## Author

Helmut Hoffer von Ankershoffen (hhva@20steps.de).