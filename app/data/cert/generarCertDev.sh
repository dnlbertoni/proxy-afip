#!/bin/bash

KEYPRIVADA=`hostname`_4096.ppk
EMPRESA=danielbertoni
SISTEMA=proxyafip
CUIT=20268667033
KEYPEDIDO=local.csr

openssl genrsa -out $KEYPRIVADA 4096

openssl req -new -key $KEYPRIVADA -subj "/C=AR/O=$EMPRESA/CN=$SISTEMA/serialNumber=CUIT $CUIT" -out $KEYPEDIDO