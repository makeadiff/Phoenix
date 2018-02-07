#!/bin/bash

# Regenerate docs based on the Swagger YAML.

# Bootprint...
bootprint openapi "/mnt/x/Data/www/Projects/Phoenix/api/swagger/swagger.yaml" "/mnt/x/Data/www/Projects/Phoenix/docs/bootprint"

# Spectacle
spectacle -d "/mnt/x/Data/www/Projects/Phoenix/api/swagger/swagger.yaml" -t "/mnt/x/Data/www/Projects/Phoenix/docs/spectacle"
