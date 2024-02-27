#!/bin/bash

SCRIPT_PATH="db/backup.sh"

# backup.sh
echo "Executing script: $SCRIPT_PATH"
bash "$SCRIPT_PATH"
if [ $? -eq 0 ]; then
    echo "Script executed successfully."
else
    echo "Error executing script: $SCRIPT_PATH"
    exit 1
fi

# docker-compose down
echo "Executing docker-compose down"
docker-compose down
if [ $? -eq 0 ]; then
    echo "docker-compose down executed successfully."
else
    echo "Error executing docker-compose down"
    exit 1
fi

exit 0
