<?php

namespace twentysteps\Bundle\CollmexBundle\Services;

/**
 * Class CollmexService
 *
 * This service interacts with Collmex accounting (cp. http://www.collmex.de/)
 */
class CollmexService  {

    private $logger;
    private $entityManager;
    private $cache;
    private $client;
    private $accountId;
    private $login;
    private $password;

    // inject some services, cp. services.yml
	public function __construct($logger,$entityManager,$cache,$client,$accountId,$login,$password)
    {
        $this->logger=$logger;
        $this->entityManager=$entityManager;
        $this->cache=$cache;

        $this->client=$client;
        $this->client->setSslVerification(true,false,2);

        $this->accountId=$accountId;
        $this->login=$login;
        $this->password=$password;
    }

    // generic call to Collmex API using "satz" (command and args)
    // cp. http://www.collmex.de/cgi-bin/cgi.exe?1005,1,help,api_ueberblick
    protected function call($satz) {
        $this->logger->info('making call');
        // do not remove the ',' in the end - have to trick guzzle into not adding a = after exchange ...
        $call="https://www.collmex.de/cgi-bin/cgi.exe?".$this->accountId.",0,data_exchange,";
        $request = $this->client->post($call);
        $request->getQuery()->useUrlEncoding(false);
        $request->setBody("LOGIN;".$this->login.";".$this->password."\n".$satz."\n","text/csv");
        $csv=$request->send()->getBody(true);

        $rows=array();
        foreach (explode("\n",$csv) as $line) {
            $rows[]=str_getcsv(mb_convert_encoding($line,'utf-8'),';');
        }
        return $rows;
    }

    protected function processCustomerRow($row) {
        $customer=null;
        if ($row[0]=='CMXKND') {
            $customer=array(
                'id' => $row['1'],
                'salutation' => $row['3'],
                'title' => $row['4'],
                'name_first' => $row['5'],
                'name_last' => $row['6'],
                'company' => $row['7'],
                'department' => $row['8'],
                'street' => $row['9'],
                'zip' => $row['10'],
                'city' => $row['11'],
                'comment' => $row['12'],
                'country' => $row['14'],
                'phone' => $row['15'],
                'fax' => $row['16'],
                'email' => $row['17'],
                'bank_account_nr' => $row['18'],
                'bank_code' => $row['19'],
                'bank_iban' => $row['20'],
                'bank_bic' => $row['21'],
                'bank_name' => $row['22'],
                'tax_nr' => $row['23'],
                'tax_purchase_nr' => $row['24'],
                'bank_account_name' => $row['30'],
                'debit_sepa' => $row['44']
            );                
        }
        return $customer;
    }

    public function getCustomers($config=array()) {
        $rows=$this->call('CUSTOMER_GET;;1');
        $customers=array();
        foreach ($rows as $row) {
            $customer=$this->processCustomerRow($row);
            if ($customer) {
                $customers[$customer['id']]=$customer;
            }

        }
        return $customers;
    }

    public function getCustomer($config) {
        $rows=$this->call('CUSTOMER_GET;'.$config['id'].';1');
        $customers=array();
        foreach ($rows as $row) {
            $customer=$this->processCustomerRow($row);
            if ($customer) {
                return $customer;
            }
        }
        return null;
    }

    public function getCustomerCount() {
    	return count($this->getCustomers());
    }

    public function processInvoiceRow($row) {
        $invoice=null;
        if ($row[0]=='CMXINV') {
            $invoice=array(
                'id' => $row['1'],
                'position' => $row['2'],
                'type' => $row['3'],
                'order_id' => $row['5'],
                'customer_id' => $row['6'],
                'company' => $row['7'],
                'deleted' => $row['41'],
                'status' => $row['46'],
                'invoice_date' => $row['29'],
                'unit_quantity' => $row['72'],
                'unit_price' => $row['73'],
                'unit_discount' => $row['74'],
                'revenue' => $row['83']
            );                
        }
        return $invoice;
    }

    public function getInvoices($config=array()) {
    	if (!is_array($config)) {
    		return null;
    	}
    	if (!array_key_exists('customer_id',$config)) {
    		$config['customer_id']='';
    	}
        $rows=$this->call('INVOICE_GET;;;'.$config['customer_id'].';1');
        $invoices=array();
        foreach ($rows as $row) {
            $invoice=$this->processInvoiceRow($row);
            if ($invoice) {
                $invoices[$invoice['id']]=$invoice;
            }
        }
        return $invoices;
    }

    public function getInvoiceCount() {
    	return count($this->getInvoices());
    }

    public function processOrderRow($row) {
        $order=null;
        if ($row[0]=='CMXORD-2') {
            $order=array(
                'id' => $row['1'],
                'product_id' => $row['72'],
                'product_description' => $row['74'],
            );                
        }
        return $order;
    }
    
    public function getOrders($config=array()) {
    	if (!is_array($config)) {
    		return null;
    	}
    	if (!array_key_exists('customer_id',$config)) {
    		$config['customer_id']='';
    	}
        $rows=$this->call('SALES_ORDER_GET;;;'.$config['customer_id'].';1');
        $orders=array();
        foreach ($rows as $row) {
            $order=$this->processOrderRow($row);
            if ($order) {
                if (array_key_exists('include_products',$config) && $config['include_products']==true) {
                    $order['product']=$this->getProduct(array('product_id' => $order['product_id']));
                }
                $orders[$order['id']]=$order;
            }
        }
        return $orders;
    }

    public function getOrderCount() {
    	return count($this->getOrders());
    }

    public function processProductRow($row) {
        $product=null;
        if ($row[0]=='CMXPRD') {
            $product=array(
                'id' => $row['1'],
                'name' => $row['2'],
            );                
        }
        return $product;
    }

    public function getKPIs() {
    	return array(
    		'customers' => array(
    			'count' => $this->getCustomerCount()
    		),
    		'orders' => array(
    			'count' => $this->getOrderCount()
    		),
    		'invoices' => array(
    			'count' => $this->getInvoiceCount()
    		),
    		'products' => array(
    			'count' => $this->getProductCount()
    		)
    	);
    }
    
    public function getProducts($config) {
    	if (!is_array($config)) {
    		return null;
    	}
        $rows=$this->call('PRODUCT_GET;1');
        $products=array();
        foreach ($rows as $row) {
            $product=$this->processProductRow($row);
            $products[$product['id']]=$product;
        }
        return $products;
    }

    public function getProduct($config) {

        $rows=$this->call('PRODUCT_GET;1;'.$config['product_id']);
        foreach ($rows as $row) {
            $product=$this->processProductRow($row);
            if ($product) {
                return $product;
            }
        }
        return null;
    }

    public function getProductCount() {
    	return count($this->getProducts(array()));
    }

    


 }