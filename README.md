# 20steps/collmex-bundle (twentystepsCollmexBundle)

## About

The twentystepsCollmexBundle provides a Service-oriented API for Symfony2 applications that need to interact with the Collmex accounting service.

For further information about Collmex: http://www.collmex.de

## Features

* Collmex accessible as a Symfony2 service.
* Configurable caching of responses to prevent surpassing rate limit.
* Provide some derived KPIs.

## Installation

Register the bundle by adding the following line to the registerBundles() method of your AppKernel.php:  
new twentysteps\Bundle\CollmexBundle\twentystepsCollmexBundle()" to the registerBundles() method of your AppKernel.php

Register services provided by the bundle by adding the following line to the imports section of your config.yml:  
\- { resource: "@twentystepsCollmexBundle/Resources/config/services.yml" } to the imports section of your config.yml

Define the following properties in your parameters.yml:  
* twentysteps_collmex.url - URL of the Collmex API - normally should point to "https://www.collmex.de".
* twentysteps_collmex.account_id - ID of your account at Collmex. You will need a "Pro" account at collmex use this API.
* twentysteps_collmex.login - The login to use for accessing Collmex. You should create a service account to not interfere with the sessions of your accountants.
* twentysteps_collmex.password - Password of the account.

## Version

This version is not yet complete or usable.

## Author

Helmut Hoffer von Ankershoffen (hhva@20steps.de).