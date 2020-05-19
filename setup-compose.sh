#!/bin/bash

# Exit on error
set -e

# build docker image
image=$(docker images|grep mynotes|awk '{print $1}')
if [[ -z $image ]]; then
  docker build -t mynotes:v1 .
fi

docker-compose up -d

# create table user, note and insert an user hxy into table user
if [ $? -eq 0 ]; then
  myapp=$(docker ps|grep mynotes_app|awk '{print $1}')
  mysql=$(docker ps|grep mynotes_mysql|awk '{print $1}')

  # wait until container mysql and app are ready
  while [[ -z $mysql || -z $myapp ]]; do
    sleep 5s
    mysql=$(docker ps|grep mynotes_mysql|awk '{print $1}')
    myapp=$(docker ps|grep mynotes_app|awk '{print $1}')
  done

  if [[ -n $mysql ]]; then
    echo "inside $myapp"
    result=$(docker exec $myapp php -f ./dev/seed.php)
    echo $result
    error="Connection refused"
    # if mysql is not ready and refuse the connection, retry
    if [[ $result =~ $error ]]; then
      count=0
      while [[ $result =~ $error ]]; do
        count=$[count+1]
        sleep 2s
        result=$(docker exec $myapp php -f ./dev/seed.php)
        echo "retry $count"
        echo $result
        if [ $count -gt 10 ]; then
          break
        fi
      done
    fi
  fi
fi
