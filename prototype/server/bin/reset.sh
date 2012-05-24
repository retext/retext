#!/bin/bash

ENV=$1

app/console --env=$ENV doctrine:mongodb:generate:documents RetextApiBundle
app/console --env=$ENV doctrine:mongodb:generate:hydrators
app/console --env=$ENV doctrine:mongodb:generate:proxies
app/console --env=$ENV doctrine:mongodb:generate:repositories RetextApiBundle
app/console --env=$ENV doctrine:mongodb:schema:drop
app/console --env=$ENV doctrine:mongodb:schema:create
