#!/bin/bash

KEYPRIVADA=`hostname`_2048.ppk
EMPRESA=danielbertoni
SISTEMA=proxyafip
CUIT=20268667033
KEYPEDIDO=$EMPRESA$SISTEMA$CUIT.csr

openssl genrsa -out $KEYPRIVADA 2048

openssl req -new -key $KEYPRIVADA -subj "/C=AR/O=$EMPRESA/CN=$SISTEMA/serialNumber=CUIT $CUIT" -out $KEYPEDIDO