Service-oriented Collmex API for Symfony2 applications

# twentystepsCollmexBundle

## About

The twentystepsCollmexBundle provides a Service-oriented API for Symfony2 applications that need to interact with the Collmex accounting service.

For further information about Collmex: http://www.collmex.de

## Features

* Collmex accessible via Symfony2 service

## Installation

Register the bundle by adding the following line to the registerBundles() method of your AppKernel.php:  
new twentysteps\Bundle\twentystepsCollmexBundle()" to the registerBundles() method of your AppKernel.php

Register services provided by the bundle by adding the following line to the imports section of your config.yml:  
\- { resource: "@twentystepsCollmexBundle/Resources/config/services.yml" } to the imports section of your config.yml

Define the following properties in your parameters.yml:  
* twentysteps_collmex.url
* twentysteps_collmex.account_id
* twentysteps_collmex.login
* twentysteps_collmex.password

## Version

This version is not yet ready for production!