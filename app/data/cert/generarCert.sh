#!/bin/bash

KEYPRIVADA=privada.key
EMPRESA=danielbertoni
SISTEMA=proxyafip
CUIT=20268667033
KEYPEDIDO=$EMPRESA$SISTEMA$CUITv1.csr

#openssl genrsa -out $KEYPRIVADA 2048

openssl req -new -key $KEYPRIVADA -subj "/C=AR/O=$EMPRESA/CN=$SISTEMA/serialNumber=CUIT $CUIT" -out $KEYPEDIDO